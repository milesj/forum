<?php
/** 
 * topic.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Topic Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class Topic extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'ForumCategory' => array(
			'className' 	=> 'Forum.ForumCategory',
			'counterCache' 	=> true
		),
		'User' => array(
			'className' 	=> 'Forum.User'
		),
		'FirstPost' => array(
			'className' 	=> 'Forum.Post',
			'foreignKey'	=> 'firstPost_id'
		),
		'LastPost' => array(
			'className' 	=> 'Forum.Post',
			'foreignKey'	=> 'lastPost_id'
		),
		'LastUser' => array(
			'className' 	=> 'Forum.User',
			'foreignKey'	=> 'lastUser_id'
		)
	);
	
	/**
	 * Has one
	 * @access public
	 * @var array
	 */
	public $hasOne = array(
		'Poll' => array(
			'className' => 'Forum.Poll',
			'dependent' => true
		)
	);
	
	/**
	 * Has many
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'Post' => array(
			'className'	=> 'Forum.Post',
			'exclusive' => true,
			'dependent' => true,
			'order' 	=> 'Post.created DESC',
		)
	);
	
	/**
	 * Validation
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'title' => 'notEmpty',
		'forum_category_id' => 'notEmpty',
		'options' => array(
			'checkOptions' => array(
				'rule' => array('checkOptions'),
				'message' => 'You must supply a minimum of 2 options and a max of 10'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please supply some answer options for your poll'
			)
		),
		'content' => 'notEmpty'
	);
	
	/**
	 * Validate and add a topic
	 * @access public
	 * @param array $data
	 * @param array $settings
	 * @param array $topics
	 * @param boolean $poll
	 * @return boolean|int
	 */
	public function addTopic($data, $settings, $topics, $poll = false) {
		$this->set($data);
		
		// Validate
		if ($this->validates()) {
			$isAdmin = ($_SESSION['Forum']['isAdmin'] > 0) ? true : false;

			if (($secondsLeft = $this->checkFlooding($topics, $settings['topic_flood_interval'])) > 0 && !$isAdmin) {
				$this->invalidate('title', 'You must wait '. $secondsLeft .' more second(s) till you can post a topic');
				
			} else if ($this->checkHourly($topics, $settings['topics_per_hour']) && !$isAdmin) {
				$this->invalidate('title', 'You are only allowed to post '. $settings['topics_per_hour'] .' topic(s) per hour');
				
			} else {
				$data['Topic']['title'] = strip_tags($data['Topic']['title']);
				
				// Save Topic
				$this->create();
				$this->save($data, false, array('forum_category_id', 'user_id', 'title', 'status', 'type'));
				
				$topic_id = $this->id;
				$user_id = $data['Topic']['user_id'];
				$post_id = $this->Post->addFirstPost($topic_id, $data['Topic']);
				
				// Update legend
				$this->update($topic_id, array(
					'firstPost_id' => $post_id,
					'lastPost_id' => $post_id,
					'lastUser_id' => $user_id,
				));
				
				$this->ForumCategory->update($data['Topic']['forum_category_id'], array(
					'lastTopic_id' => $topic_id,
					'lastPost_id' => $post_id,
					'lastUser_id' => $user_id,
				));
				
				// Update parent forum as well
				$forum = $this->ForumCategory->find('first', array(
					'conditions' => array('ForumCategory.id' => $data['Topic']['forum_category_id']),
					'fields' => array('ForumCategory.id', 'ForumCategory.parent_id'),
					'contain' => false
				));
				
				if ($forum['ForumCategory']['parent_id'] != 0) {
					$this->ForumCategory->update($forum['ForumCategory']['parent_id'], array(
						'lastTopic_id' => $topic_id,
						'lastPost_id' => $post_id,
						'lastUser_id' => $user_id,
					));	
				}
				
				// Add a poll?
				if ($poll === true) {
					$post_id = $this->Poll->addPoll($topic_id, $data['Topic']);
				}
				
				return $topic_id;
			}
		}
		
		return false;
	}
	
	/**
	 * Check the posting flood interval
	 * @access public
	 * @param array $topics
	 * @param int $interval
	 * @return boolean|int
	 */
	public function checkFlooding($topics, $interval) {
		if (!empty($topics)) {
			$lastPost = array_slice($topics, -1, 1);
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
	 * @param array $topics
	 * @param int $max
	 * @return boolean
	 */
	public function checkHourly($topics, $max) {
		$pastHour = strtotime('-1 hour');
			
		if (!empty($topics)) {
			$count = 0;
			foreach ($topics as $id => $time) {
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
	 * Check to make sure the poll is valid
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function checkOptions($data) {
		$data = array_values($data);
		$options = explode("\n", $data[0]);
		
		$clean = array();
		if (!empty($options)) {
			foreach ($options as $o) {
				if ($o != '') {
					$clean[] = $o;
				}
			}
		}
		$total = count($clean);
		
		return ($total >= 2 && $total <= 10) ? true : false;
	}

	/**
	 * Delete a topic and process any required logic
	 * @param int $id
	 * @return boolean
	 */
	public function destroy($id) {
		return $this->delete($id, true);
	}
	
	/**
	 * Robust method for saving all topic data
	 * @access public
	 * @param int $id
	 * @param array $topic
	 * @return boolean
	 */
	public function editTopic($id, $topic) {
		if (!empty($topic)) {
			foreach ($topic as $model => $data) {
				if ($model == 'Topic') {
					$this->id = $id;
					$this->save($data, false, array_keys($data));
					
				} else if ($model == 'FirstPost') {
					$this->Post->id = $data['id'];
					$this->Post->save($data, false, array('content'));
					
				} else if ($model == 'Poll') {
					$data['expires'] = (!empty($data['expires'])) ? date('Y-m-d H:i:s', strtotime('+'. $data['expires'] .' days')) : NULL;
					$this->Poll->id = $data['id'];
					$this->Poll->save($data, false, array('expires'));
					
					if (!empty($data['PollOption'])) {
						foreach ($data['PollOption'] as $option) {
							if ($option['delete'] != 0) {
								$this->Poll->PollOption->delete($option['id'], true);
							} else {
								if ($option['option'] != '') {
									$this->Poll->PollOption->id = $option['id'];
									$this->Poll->PollOption->save($option, false, array('option', 'vote_count'));
								}
							}
						}
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Increase the view count
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increaseViews($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."topics` AS `Topic` SET `Topic`.`view_count` = `Topic`.`view_count` + 1 WHERE `Topic`.`id` = $id");
	}
	
	/**
	 * Get the latest topics
	 * @access public
	 * @param int $limit
	 * @return array
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'order' => 'Topic.created DESC',
			'limit' => $limit,
			'contain' => array('User', 'LastPost.created', 'FirstPost.content')
		));
	}
	
	/**
	 * Get the latest topics by a user
	 * @access public
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getLatestByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Topic.user_id' => $user_id),
			'order' => 'Topic.created DESC',
			'limit' => $limit,
			'contain' => array('LastPost.created')
		));
	}
	
	/**
	 * Get all high level topics within a forum category
	 * @access public
	 * @param int $category_id
	 * @return array
	 */
	public function getStickiesInForum($category_id) {
		return $this->find('all', array(
			'order' => 'Topic.type DESC',
			'conditions' => array(
				'OR' => array(
					array('Topic.type' => 3),
					array(
						'Topic.forum_category_id' => $category_id,
						'Topic.type' => array(1, 2)
					)
				)
			),
			'contain' => array('User.id', 'User.username', 'LastPost.created', 'LastUser.username', 'Poll.id')
		));
	}
	
	/**
	 * Get all info for editing a topic
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getTopicForEdit($id) {
		$topic = $this->find('first', array(
			'conditions' => array('Topic.id' => $id),
			'contain' => array(
				'FirstPost.id', 'FirstPost.content', 
				'Poll' => array('PollOption'),
				'ForumCategory' => array(
					'fields' => array('ForumCategory.id', 'ForumCategory.title'),
					'Forum', 'Parent'
				)
			),
			'callbacks' => 'before'
		));
		
		if (!empty($topic['Poll']['id'])) {
			if ($topic['Poll']['expires'] != null) {
				$topic['Poll']['expires'] = $this->daysBetween($topic['Poll']['created'], $topic['Poll']['expires']);
			}
		}
		
		return $topic;
	}
	
	/**
	 * Get all info for replying to a topic
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getTopicForReply($id) {
		return $this->find('first', array(
			'fields' => array('Topic.id', 'Topic.title', 'Topic.status', 'Topic.forum_category_id'),
			'conditions' => array('Topic.id' => $id),
			'contain' => array(
				'ForumCategory' => array(
					'fields' => array('ForumCategory.id', 'ForumCategory.title', 'ForumCategory.accessReply', 'ForumCategory.settingPostCount'),
					'Forum', 'Parent'
				)
			)
		));
	}
	
	/**
	 * Get all info for reading a topic
	 * @access public
	 * @param int $id
	 * @param int $user_id
	 * @return array
	 */
	public function getTopicForViewing($id, $user_id) {
		$topic = $this->find('first', array(
			'fields' => array('Topic.id', 'Topic.title', 'Topic.status', 'Topic.type', 'Topic.forum_category_id', 'Topic.firstPost_id'),
			'conditions' => array('Topic.id' => $id),
			'contain' => array(
				'ForumCategory' => array(
					'fields' => array('ForumCategory.id', 'ForumCategory.title', 'ForumCategory.accessPost', 'ForumCategory.accessPoll', 'ForumCategory.accessReply', 'ForumCategory.accessRead'),
					'Forum', 'Parent'
				), 
				'Poll' => array('PollOption')
			)
		));
		
		if (!empty($topic['Poll']['id'])) {
			$topic['Poll'] = $this->Poll->process($topic['Poll'], $user_id);
		}
		
		return $topic;
	}
	
	/**
	 * Move all topics to a new forum
	 * @access public
	 * @param int $start_id
	 * @param int $moved_id
	 * @return boolean
	 */
	public function moveAll($start_id, $moved_id) {
		return $this->updateAll(
			array('Topic.forum_category_id' => $moved_id),
			array('Topic.forum_category_id' => $start_id)
		);
	}
	
	/**
	 * Finds difference in days between dates
	 * @access public
	 * @param int $start
	 * @param int $finish
	 * @return int
	 */
	public function daysBetween($start, $finish) {
		if (!is_int($start))	$start = strtotime($start);
		if (!is_int($finish))	$finish = strtotime($finish);
		
		$diff = $finish - $start;
		$days = $diff / 86400;
		
		return round($days);
	}
	
	/**
	 * After find
	 * @access public
	 * @param array $results
	 * @param boolean $primary
	 * @return array
	 */
	public function afterFind($results, $primary = NULL) {
		if (!empty($results)) {
			$Config = ForumConfig::getInstance();
			$postsPerPage = $Config->settings['posts_per_page'];
			$autoLock = $Config->settings['days_till_autolock'];
			
			if ($primary === true) {	
				foreach ($results as &$result) {
					if (isset($result['Topic'])) {
					
						// Get total pages
						if (!empty($result['Topic']['post_count'])) {
							$result['Topic']['page_count'] = ($result['Topic']['post_count'] > $postsPerPage) ? ceil($result['Topic']['post_count'] / $postsPerPage) : 1;
						} else {
							$result['Topic']['page_count'] = 1;
						}
						
						// Automatically lock threads
						if (!empty($result['Topic']['forum_category_id'])) {
							$forum = ClassRegistry::init('Forum.ForumCategory')->find('first', array(
								'fields' => array('ForumCategory.settingAutoLock'),
								'conditions' => array('ForumCategory.id' => $result['Topic']['forum_category_id']),
								'contain' => false
							));
							$lock = ($forum['ForumCategory']['settingAutoLock'] == 1) ? 'yes' : 'no';
						} else {
							$lock = 'yes';
						}
			
						if (isset($result['LastPost']['created'])) {
							$lastTime = $result['LastPost']['created'];
						} else if (isset($result['Topic']['modified'])) {
							$lastTime = $result['Topic']['modified'];
						}
						
						if (!empty($lastTime) && $lock == 'yes') {
							if (strtotime($lastTime) < strtotime('-'. $autoLock .' days')) {
								$result['Topic']['status'] = 1;
							}
						}
					}
				}
				
			} else {
				// Get total pages
				if (!empty($results['post_count'])) {
					$results['page_count'] = ($results['post_count'] > $postsPerPage) ? ceil($results['post_count'] / $postsPerPage) : 1;
				} else {
					$results['page_count'] = 1;
				}
			}
		}
		
		return $results;
	}
	
}
