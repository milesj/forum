<?php
/** 
 * Forum - Topic Model
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class Topic extends ForumAppModel {

	/**
	 * Type constants.
	 */
	const NORMAL = 0;
	const STICKY = 1;
	const IMPORTANT = 2;
	const ANNOUNCEMENT = 3;

	/**
	 * Behaviors
	 *
	 * @access public
	 * @var array
	 */
	public $actsAs = array(
		'Utils.Sluggable' => array(
			'separator' => '-'
		)
	);

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'User',
		'Forum' => array(
			'className' 	=> 'Forum.Forum',
			'counterCache' 	=> true
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
			'className'		=> 'User',
			'foreignKey'	=> 'lastUser_id'
		)
	);
	
	/**
	 * Has one.
	 *
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
	 * Has many.
	 *
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'Post' => array(
			'className'	=> 'Forum.Post',
			'exclusive' => true,
			'dependent' => true,
			'order' 	=> array('Post.created' => 'DESC'),
		)
	);
	
	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'title' => 'notEmpty',
		'forum_id' => 'notEmpty',
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
	 * Check the posting flood interval.
	 *
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
	 * Check the hourly posting.
	 *
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
	 * Check to make sure the poll is valid.
	 *
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
	 * Delete a topic and process any required logic.
	 *
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function destroy($id) {
		return $this->delete($id, true);
	}

	/**
	 * Increase the view count.
	 *
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increaseViews($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."topics` AS `Topic` SET `Topic`.`view_count` = `Topic`.`view_count` + 1 WHERE `Topic`.`id` = $id");
	}
	
	/**
	 * Get the latest topics.
	 *
	 * @access public
	 * @param int $limit
	 * @return array
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'order' => array('Topic.created' => 'DESC'),
			'limit' => $limit,
			'contain' => array('User', 'LastPost.created', 'FirstPost.content')
		));
	}
	
	/**
	 * Get the latest topics by a user.
	 *
	 * @access public
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getLatestByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Topic.user_id' => $user_id),
			'order' => array('Topic.created' => 'DESC'),
			'limit' => $limit,
			'contain' => array('LastPost.created')
		));
	}
	
	/**
	 * Get all high level topics within a forum.
	 *
	 * @access public
	 * @param int $forum_id
	 * @return array
	 */
	public function getStickiesInForum($forum_id) {
		return $this->find('all', array(
			'order' => array('Topic.type' => 'DESC'),
			'conditions' => array(
				'OR' => array(
					array('Topic.type' => self::ANNOUNCEMENT),
					array(
						'Topic.forum_id' => $forum_id,
						'Topic.type' => array(self::STICKY, self::IMPORTANT)
					)
				)
			),
			'contain' => array('User.id', 'User.username', 'LastPost.created', 'LastUser.username', 'Poll.id')
		));
	}
	
	/**
	 * Get all info for editing a topic.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getTopicForEdit($id) {
		$topic = $this->find('first', array(
			'conditions' => array('Topic.id' => $id),
			'contain' => array(
				'FirstPost', 
				'Poll' => array('PollOption'),
				'Forum' => array('Parent')
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
	 * Get all info for replying to a topic.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getTopicForReply($id) {
		return $this->find('first', array(
			'fields' => array('Topic.id', 'Topic.title', 'Topic.slug', 'Topic.status', 'Topic.forum_id'),
			'conditions' => array('Topic.id' => $id),
			'contain' => array(
				'Forum' => array(
					'fields' => array('Forum.id', 'Forum.title', 'Forum.slug', 'Forum.accessReply', 'Forum.settingPostCount'),
					'Parent'
				)
			)
		));
	}
	
	/**
	 * Get all info for reading a topic.
	 *
	 * @access public
	 * @param string $slug
	 * @param int $user_id
	 * @return array
	 */
	public function getTopicForViewing($slug, $user_id, $field = 'slug') {
		$topic = $this->find('first', array(
			'fields' => array('Topic.id', 'Topic.title', 'Topic.slug', 'Topic.status', 'Topic.type', 'Topic.forum_id', 'Topic.firstPost_id'),
			'conditions' => array('Topic.'. $field => $slug),
			'contain' => array(
				'Forum' => array(
					'fields' => array('Forum.id', 'Forum.title', 'Forum.slug', 'Forum.accessPost', 'Forum.accessPoll', 'Forum.accessReply', 'Forum.accessRead'),
					'Parent'
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
	 * Finds difference in days between dates.
	 *
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
	 * After find.
	 * 
	 * @access public
	 * @param array $results
	 * @param boolean $primary
	 * @return array
	 */
	public function afterFind($results, $primary = NULL) {
		if (!empty($results)) {
			$postsPerPage = Configure::read('Forum.settings.posts_per_page');
			$autoLock = Configure::read('Forum.settings.days_till_autolock');
			
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
						if (!empty($result['Topic']['forum_id'])) {
							$forum = ClassRegistry::init('Forum.Forum')->find('first', array(
								'fields' => array('Forum.settingAutoLock'),
								'conditions' => array('Forum.id' => $result['Topic']['forum_id']),
								'contain' => false
							));
							$lock = ($forum['Forum']['settingAutoLock'] == 1) ? 'yes' : 'no';
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
	
	/**
	 * NEW
	 */
	
	/**
	 * Validate and add a topic.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean|int
	 */
	public function addTopic($data) {
		$this->set($data);
		
		if ($this->validates()) {
			$isAdmin = $this->Session->read('Forum.isAdmin');
			$topics = $this->Session->read('Forum.topics');

			if (($secondsLeft = $this->checkFlooding($topics, $this->settings['topic_flood_interval'])) > 0 && !$isAdmin) {
				return $this->invalidate('title', 'You must wait '. $secondsLeft .' more second(s) till you can post a topic');
				
			} else if ($this->checkHourly($topics, $this->settings['topics_per_hour']) && !$isAdmin) {
				return $this->invalidate('title', 'You are only allowed to post '. $this->settings['topics_per_hour'] .' topic(s) per hour');
				
			} else {
				$data['title'] = Sanitize::clean($data['title']);

				$this->create();
				$this->save($data, false, array('forum_id', 'user_id', 'title', 'slug', 'status', 'type'));
				
				$topic_id = $this->id;
				$user_id = $data['user_id'];
				$post_id = $this->Post->addFirstPost($topic_id, $data);

				$this->update($topic_id, array(
					'firstPost_id' => $post_id,
					'lastPost_id' => $post_id,
					'lastUser_id' => $user_id,
				));
				
				$this->Forum->update($data['forum_id'], array(
					'lastTopic_id' => $topic_id,
					'lastPost_id' => $post_id,
					'lastUser_id' => $user_id,
				));
				
				// Update parent forum
				$forum = $this->Forum->getById($data['forum_id']);
				
				if (!empty($forum) && $forum['Forum']['forum_id'] != 0) {
					$this->Forum->update($forum['Forum']['forum_id'], array(
						'lastTopic_id' => $topic_id,
						'lastPost_id' => $post_id,
						'lastUser_id' => $user_id,
					));	
				}
				
				// Add a poll?
				if (isset($data['options'])) {
					$post_id = $this->Poll->addPoll($topic_id, $data);
				}
				
				return $topic_id;
			}
		}
		
		return false;
	}
	
	/**
	 * Robust method for saving all topic data.
	 *
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
					$data['expires'] = !empty($data['expires']) ? date('Y-m-d H:i:s', strtotime('+'. $data['expires'] .' days')) : null;
					
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
	 * Get all info for reading a topic.
	 *
	 * @access public
	 * @param string $slug
	 * @param int $user_id
	 * @return array
	 */
	public function get($slug) {
		$topic = $this->find('first', array(
			'conditions' => array('Topic.slug' => $slug),
			'contain' => array(
				'FirstPost', 
				'Forum' => array('Parent'), 
				'Poll' => array('PollOption')
			)
		));
		
		if (!empty($topic['Poll']['id'])) {
			$topic['Poll'] = $this->Poll->process($topic['Poll']);
		}
		
		return $topic;
	}
	
	/**
	 * Return a topic based on ID.
	 * 
	 * @acccess public
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Topic.id' => $id)
		));
	}
	
	/**
	 * Move all topics to a new forum.
	 *
	 * @access public
	 * @param int $start_id
	 * @param int $moved_id
	 * @return boolean
	 */
	public function moveAll($start_id, $moved_id) {
		return $this->updateAll(
			array('Topic.forum_id' => $moved_id),
			array('Topic.forum_id' => $start_id)
		);
	}
	
}
