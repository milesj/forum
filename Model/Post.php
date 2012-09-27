<?php
/**
 * Forum - Post
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');
App::import('Vendor', 'Forum.Decoda', array(
	'file' => 'decoda/Decoda.php'
));

class Post extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Forum' => array(
			'className' => 'Forum.Forum',
			'counterCache' => true
		),
		'Topic' => array(
			'className' => 'Forum.Topic',
			'counterCache' => true
		),
		'User' => array(
			'className' => FORUM_USER
		)
	);

	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'content' => 'notEmpty'
	);

	/**
	 * Validate and add a post.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean|int
	 */
	public function add($data) {
		$this->set($data);

		if ($this->validates()) {
			$isAdmin = $this->Session->read('Forum.isAdmin');

			if (($secondsLeft = $this->checkFlooding($this->settings['post_flood_interval'])) > 0 && !$isAdmin) {
				return $this->invalidate('content', 'You must wait %s more second(s) till you can post a reply', $secondsLeft);

			} else if ($this->checkHourly($this->settings['posts_per_hour']) && !$isAdmin) {
				return $this->invalidate('content', 'You are only allowed to post %s time(s) per hour', $this->settings['posts_per_hour']);

			} else {
				$this->create();
				$this->save($data, false, array('topic_id', 'forum_id', 'user_id', 'userIP', 'content', 'contentHtml'));

				$data['post_id'] = $this->id;

				$this->Topic->update($data['topic_id'], array(
					'lastPost_id' => $data['post_id'],
					'lastUser_id' => $data['user_id'],
				));

				$this->Topic->Forum->chainUpdate($data['forum_id'], array(
					'lastTopic_id' => $data['topic_id'],
					'lastPost_id' => $data['post_id'],
					'lastUser_id' => $data['user_id']
				));

				return $data['post_id'];
			}
		}

		return false;
	}

	/**
	 * Save the first post with a topic.
	 *
	 * @access public
	 * @param array $data
	 * @return int
	 */
	public function addFirstPost($data) {
		$this->create();

		$this->save(array(
			'topic_id' => $data['topic_id'],
			'forum_id' => $data['forum_id'],
			'user_id' => $data['user_id'],
			'userIP' => $data['userIP'],
			'content' => $data['content']
		), false);

		return $this->id;
	}

	/**
	 * Check the posting flood interval.
	 *
	 * @access public
	 * @param int $interval
	 * @return boolean
	 */
	public function checkFlooding($interval) {
		if ($posts = $this->Session->read('Forum.posts')) {
			$timeLeft = time() - array_pop($posts);

			if ($timeLeft <= $interval) {
				return $interval - $timeLeft;
			}
		}

		return false;
	}

	/**
	 * Check the hourly posting.
	 *
	 * @access public
	 * @param int $max
	 * @return boolean
	 */
	public function checkHourly($max) {
		$pastHour = strtotime('-1 hour');

		if ($posts = $this->Session->read('Forum.posts')) {
			$count = 0;

			foreach ($posts as $time) {
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
	 * Return a post based on ID.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Post.id' => $id),
			'contain' => array(
				'Topic', 'User',
				'Forum' => array('Parent')
			),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Return a list of IDs within a topic.
	 *
	 * @access public
	 * @param int $topic_id
	 * @return array
	 */
	public function getIdsForTopic($topic_id) {
		return $this->find('list', array(
			'conditions' => array('Post.topic_id' => $topic_id),
			'order' => array('Post.id' => 'ASC')
		));
	}

	/**
	 * Return the latest posts by a user.
	 *
	 * @access public
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getLatestByUser($user_id, $limit = 5) {
		return $this->find('all', array(
			'conditions' => array('Post.user_id' => $user_id),
			'order' => array('Post.created' => 'DESC'),
			'limit' => $limit,
			'contain' => array(
				'Topic' => array('User')
			)
		));
	}

	/**
	 * Return the latest posts by a user, grouped by the topic ID.
	 *
	 * @access public
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getGroupedLatestByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.user_id' => $user_id),
			'order' => array('Post.created' => 'DESC'),
			'group' => array('Post.topic_id'),
			'limit' => $limit,
			'contain' => array(
				'Topic' => array('LastUser', 'LastPost', 'User')
			),
			'cache' => array(__METHOD__, $user_id, $limit),
			'cacheExpires' => '+5 minutes'
		));
	}

	/**
	 * Return a post for quoting.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getQuote($id) {
		return $this->find('first', array(
			'conditions' => array('Post.id' => $id),
			'contain' => array('User')
		));
	}

	/**
	 * Return the latest posts in a topic.
	 *
	 * @access public
	 * @param int $topic_id
	 * @param int $limit
	 * @return array
	 */
	public function getTopicReview($topic_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.topic_id' => $topic_id),
			'contain' => array('User'),
			'order' => array('Post.created' => 'DESC'),
			'limit' => $limit
		));
	}

	/**
	 * Parse the HTML version.
	 *
	 * @access public
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options) {
		if (isset($this->data['Post']['content'])) {
			return $this->validateDecoda('Post');
		}

		return true;
	}

}
