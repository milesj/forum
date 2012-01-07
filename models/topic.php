<?php
/** 
 * Forum - Topic
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
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
			'separator' => '-',
			'update' => true
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
		),
		'Subscription' => array(
			'className'	=> 'Forum.Subscription',
			'exclusive' => true,
			'dependent' => true
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
		'expires' => array(
			'rule' => 'numeric',
			'message' => 'Expiration must be a numerical value for days',
			'allowEmpty' => true
		),
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
	 * Validate and add a topic.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean|int
	 */
	public function add($data) {
		$this->set($data);
		
		if ($this->validates()) {
			$isAdmin = $this->Session->read('Forum.isAdmin');

			if (($secondsLeft = $this->checkFlooding($this->settings['topic_flood_interval'])) > 0 && !$isAdmin) {
				return $this->invalidate('title', 'You must wait %s more second(s) till you can post a topic', $secondsLeft);
				
			} else if ($this->checkHourly($this->settings['topics_per_hour']) && !$isAdmin) {
				return $this->invalidate('title', 'You are only allowed to post %s topic(s) per hour', $this->settings['topics_per_hour']);
				
			} else {
				$data['title'] = Sanitize::clean($data['title']);

				$this->create();
				$this->save($data, false, array('forum_id', 'user_id', 'title', 'slug', 'status', 'type'));
				
				$data['topic_id'] = $this->id;
				$data['post_id'] = $this->Post->addFirstPost($data);

				$this->update($data['topic_id'], array(
					'firstPost_id' => $data['post_id'],
					'lastPost_id' => $data['post_id'],
					'lastUser_id' => $data['user_id'],
				));
				
				$this->Forum->chainUpdate($data['forum_id'], array(
					'lastTopic_id' => $data['topic_id'],
					'lastPost_id' => $data['post_id'],
					'lastUser_id' => $data['user_id'],
				));

				if (isset($data['options'])) {
					$this->Poll->addPoll($data);
				}
				
				// Subscribe
				if ($this->settings['auto_subscribe_self']) {
					$this->Subscription->subscribeToTopic($data['user_id'], $data['topic_id']);
				}
				
				return $data['topic_id'];
			}
		}
		
		return false;
	}
	
	/**
	 * Check the posting flood interval.
	 *
	 * @access public
	 * @param int $interval
	 * @return boolean|int
	 */
	public function checkFlooding($interval) {
		$topics = $this->Session->read('Forum.topics');
		
		if (!empty($topics)) {
			$timeLeft = time() - array_pop($topics);

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
		$topics = $this->Session->read('Forum.topics');
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
			foreach ($options as $option) {
				if ($option !== '') {
					$clean[] = $option;
				}
			}
		}

		$total = count($clean);
		
		return ($total >= 2 && $total <= 10);
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
		if (!is_int($start)) {
			$start = strtotime($start);
		}
		
		if (!is_int($finish)) {
			$finish = strtotime($finish);
		}
		
		$diff = $finish - $start;
		$days = $diff / 86400;
		
		return round($days);
	}

	/**
	 * Robust method for saving all topic data.
	 *
	 * @access public
	 * @param int $id
	 * @param array $topic
	 * @return boolean
	 */
	public function edit($id, $topic) {
		if (!empty($topic)) {
			foreach ($topic as $model => $data) {
				if ($model == 'Topic') {
					$this->id = $id;
					$this->save($data, false);
					
				} else if ($model == 'FirstPost') {
					$this->Post->id = $data['id'];
					$this->Post->save($data, false, array('content', 'contentHtml'));
					
				} else if ($model == 'Poll') {
					$data['expires'] = !empty($data['expires']) ? date('Y-m-d H:i:s', strtotime('+'. $data['expires'] .' days')) : null;
					
					$this->Poll->id = $data['id'];
					$this->Poll->save($data, false, array('expires'));
					
					if (!empty($data['PollOption'])) {
						foreach ($data['PollOption'] as $option) {
							if ($option['delete']) {
								$this->Poll->PollOption->delete($option['id'], true);
								
							} else if ($option['option'] !== '') {
								$this->Poll->PollOption->id = $option['id'];
								$this->Poll->PollOption->save($option, false, array('option', 'vote_count'));
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
			),
			'cache' => __FUNCTION__ .'-'. $slug
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
	 * Get the latest topics.
	 *
	 * @access public
	 * @param int $limit
	 * @return array
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'order' => array('Topic.created' => 'DESC'),
			'contain' => array('User', 'LastPost', 'FirstPost'),
			'limit' => $limit,
			'cache' => array(__FUNCTION__ .'-'. $limit, '+5 minutes')
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
			'contain' => array('LastPost', 'LastUser'),
			'limit' => $limit,
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
			'contain' => array('User', 'LastPost', 'LastUser', 'Poll')
		));
	}
		
	/**
	 * Increase the view count.
	 *
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increaseViews($id) {
		return $this->query('UPDATE `'. $this->tablePrefix .'topics` AS `Topic` SET `Topic`.`view_count` = `Topic`.`view_count` + 1 WHERE `Topic`.`id` = '. (int) $id);
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
	
	/**
	 * Parse the HTML version.
	 * 
	 * @access public
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options) {
		if (isset($this->data['Topic']['content'])) {
			return $this->validateDecoda('Topic');
		}
		
		return true;
	}
	
	/**
	 * After find.
	 * 
	 * @access public
	 * @param array $results
	 * @param boolean $primary
	 * @return array
	 */
	public function afterFind($results, $primary = false) {
		if (!empty($results)) {
			$postsPerPage = $this->settings['posts_per_page'];
			$autoLock = $this->settings['days_till_autolock'];
			
			if (isset($results[0])) {
				foreach ($results as &$result) {
					if (isset($result['Topic'])) {
						$lock = isset($result['Forum']) ? $result['Forum']['settingAutoLock'] : false;

						if (isset($result['LastPost'])) {
							$lastTime = $result['LastPost']['created'];
						} else if (isset($result['Topic']['modified'])) {
							$lastTime = $result['Topic']['modified'];
						}

						if (isset($result['Topic']['post_count']) && $postsPerPage) {
							$result['Topic']['page_count'] = ($result['Topic']['post_count'] > $postsPerPage) ? ceil($result['Topic']['post_count'] / $postsPerPage) : 1;
						}

						if ($lock && $lastTime && (strtotime($lastTime) < strtotime('-'. $autoLock .' days'))) {
							$result['Topic']['status'] = 1;
						}
					}
				}
			} else if (isset($results['post_count'])) {
				$results['page_count'] = ($results['post_count'] > $postsPerPage) ? ceil($results['post_count'] / $postsPerPage) : 1;
			}
		}
		
		return $results;
	}

}
