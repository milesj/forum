<?php
/** 
 * post.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Post Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum  
 */
 
class Post extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Topic' => array(
			'className'		=> 'Forum.Topic',
			'counterCache' 	=> true
		),
		'User' => array(
			'className' 	=> 'Forum.User'
		)
	);
	
	/**
	 * Validation
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'content' => 'notEmpty'
	);
	
	/**
	 * Validate and add a post
	 * @access public
	 * @param array $data
	 * @param array $settings
	 * @param array $posts
	 * @return boolean|int
	 */
	public function addPost($data, $settings, $posts) {
		$this->set($data);
		
		// Validate
		if ($this->validates()) {
			$isAdmin = ($_SESSION['Forum']['isAdmin'] > 0) ? true : false;

			if (($secondsLeft = $this->checkFlooding($posts, $settings['post_flood_interval'])) > 0 && !$isAdmin) {
				$this->invalidate('content', 'You must wait '. $secondsLeft .' more second(s) till you can post a reply');
				
			} else if ($this->checkHourly($posts, $settings['posts_per_hour']) && !$isAdmin) {
				$this->invalidate('content', 'You are only allowed to post '. $settings['topics_per_hour'] .' time(s) per hour');
				
			} else {
				$data['Post']['content'] = strip_tags($data['Post']['content']);
				
				// Save Topic
				$this->create();
				$this->save($data, false, array('topic_id', 'user_id', 'userIP', 'content'));
				
				$topic_id = $data['Post']['topic_id'];
				$user_id = $data['Post']['user_id'];
				$post_id = $this->id;
				
				// Update legend
				$this->Topic->update($topic_id, array(
					'lastPost_id' => $post_id,
					'lastUser_id' => $user_id,
				));
				
				$topic = $this->Topic->find('first', array(
					'conditions' => array('Topic.id' => $topic_id),
					'fields' => array('Topic.forum_category_id'),
					'contain' => array(
						'ForumCategory' => array(
							'fields' => array('ForumCategory.id', 'ForumCategory.parent_id'),
							'Parent'
						)
					)
				));
				
				// Get total posts for forum category
				$totalPosts = $this->find('count', array(
					'conditions' => array('Topic.forum_category_id' => $topic['Topic']['forum_category_id']),
					'contain' => array('Topic.forum_category_id')
				));
				
				$this->Topic->ForumCategory->update($topic['Topic']['forum_category_id'], array(
					'lastTopic_id' => $topic_id,
					'lastPost_id' => $post_id,
					'lastUser_id' => $user_id,
					'post_count' => $totalPosts
				));
				
				// Update parent forum as well
				if (isset($topic['ForumCategory']['Parent']['id']) && $topic['ForumCategory']['parent_id'] != 0) {
					$this->Topic->ForumCategory->update($topic['ForumCategory']['Parent']['id'], array(
						'lastTopic_id' => $topic_id,
						'lastPost_id' => $post_id,
						'lastUser_id' => $user_id,
					));	
				}
				
				return $post_id;
			}
		}
		
		return false;
	}
	
	/**
	 * Save the first post with a topic
	 * @access public
	 * @param int $topic_id
	 * @param array $data
	 * @return int
	 */
	public function addFirstPost($topic_id, $data) {
		$post = array(
			'topic_id' => $topic_id,
			'user_id' => $data['user_id'],
			'userIP' => $data['userIP'],
			'content' => strip_tags($data['content'])
		);
		
		$this->create();
		$this->save($post, false, array_keys($post));
		$this->Topic->ForumCategory->increasePosts($data['forum_category_id']);
		
		return $this->id;
	}
	
	/**
	 * Check the posting flood interval
	 * @access public
	 * @param array $posts
	 * @return boolean
	 */
	public function checkFlooding($posts, $interval) {
		if (!empty($topics)) {
			$lastPost = array_slice($posts, -1, 1);
			$lastTime = $lastPost[0];
		}

		if (isset($lastTime)) {
			$timeLeft = time() - $lastTime;
			
			if ($timeLeft <= $interval) {
				return $interval - $timeLeft;
			}
		}
		
		return false;
	}
	
	/**
	 * Check the hourly posting
	 * @access public
	 * @param array $posts
	 * @param int $max
	 * @return boolean
	 */
	public function checkHourly($posts, $max) {
		$pastHour = strtotime('-1 hour');
			
		if (!empty($posts)) {
			$count = 0;
			foreach ($posts as $id => $time) {
				if ($time >= $pastHour) {
					++$count;
				}
			}
			
			if ($count >= $max) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Delete a post and process any required logic
	 * @param int $id
	 * @param array $post
	 * @return boolean
	 */
	public function destroy($id, $post = array()) {
		if (empty($post)) {
			$post = $this->get($id, array('id'), array('Topic.id', 'Topic.forum_category_id'));
		}
		
		if (!empty($post)) {
			$totalPosts = $this->find('count', array(
				'conditions' => array('Topic.forum_category_id' => $post['Topic']['forum_category_id']),
				'contain' => array('Topic.forum_category_id')
			));

			$this->Topic->ForumCategory->update($post['Topic']['forum_category_id'], array('post_count' => $totalPosts));
		}

		return $this->delete($id, true);
	}
	
	/**
	 * Get the latest posts by a user
	 * @access public
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getLatestByUser($user_id, $limit = 5) {
		return $this->find('all', array(
			'conditions' => array('Post.user_id' => $user_id),
			'order' => 'Post.created DESC',
			'limit' => $limit,
			'contain' => array(
				'Topic' => array(
					'fields' => array('Topic.id', 'Topic.title', 'Topic.user_id'),
					'User.id', 'User.username'
				)
			)
		));
	}
	
	/**
	 * Get all info for editing a post
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getPostForEdit($id) {
		return $this->find('first', array(
			'conditions' => array('Post.id' => $id),
			'contain' => array(
				'Topic' => array(
					'fields' => array('Topic.id', 'Topic.title'),
					'ForumCategory' => array(
						'fields' => array('ForumCategory.id', 'ForumCategory.title'),
						'Forum', 'Parent'
					)
				)
			)
		));
	}
	
	/**
	 * Get a post for quoting
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getQuote($id) {
		return $this->find('first', array(
			'conditions' => array('Post.id' => $id),
			'fields' => array('Post.content'),
			'contain' => array('User.username')
		));
	}
	
	/**
	 * Get the latest posts in a topic
	 * @access public
	 * @param int $topic_id
	 * @param int $imit
	 * @return array
	 */
	public function getTopicReview($topic_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.topic_id' => $topic_id),
			'contain' => array('User.id', 'User.username', 'User.created'),
			'limit' => $limit
		));
	}

}
