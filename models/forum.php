<?php
/** 
 * Forum - Forum Model
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class Forum extends ForumAppModel {

	/**
	 * Behaviors.
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
		'Parent' => array(
			'className'		=> 'Forum.Forum',
			'foreignKey'	=> 'forum_id',
			'fields'		=> array('Parent.id', 'Parent.title', 'Parent.slug', 'Parent.forum_id')
		),
		'LastTopic' => array(
			'className' 	=> 'Forum.Topic',
			'foreignKey'	=> 'lastTopic_id'
		),
		'LastPost' => array(
			'className' 	=> 'Forum.Post',
			'foreignKey'	=> 'lastPost_id'
		),
		'LastUser' => array(
			'className' 	=> 'Forum.User',
			'foreignKey'	=> 'lastUser_id'
		),
		'AccessLevel' => array(
			'className' 	=> 'Forum.AccessLevel'
		)
	);

	/**
	 * Has many.
	 *
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'Topic' => array(
			'className'		=> 'Forum.Topic',
			'dependent'		=> false
		),
		'SubForum' => array(
			'className' 	=> 'Forum.Forum',
			'foreignKey' 	=> 'forum_id',
			'order' 		=> array('SubForum.orderNo' => 'ASC'),
			'fields' 		=> array('SubForum.id', 'SubForum.forum_id', 'SubForum.title', 'SubForum.slug', 'SubForum.description', 'SubForum.status', 'SubForum.topic_count', 'SubForum.post_count', 'SubForum.lastTopic_id', 'SubForum.lastPost_id', 'SubForum.lastUser_id'),
			'dependent'		=> false
		),
		'Moderator' => array(
			'className'		=> 'Forum.Moderator',
			'dependent'		=> true,
			'exclusive'		=> true
		)
	);
	
	/**
	 * Validate.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'title' => 'notEmpty',
		'description' => 'notEmpty',
		'forum_id' => 'notEmpty',
		'orderNo' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		)
	);
	
	/**
	 * Get the list of forums for the board index.
	 *
	 * @access public
	 * @param int $access
	 * @return array
	 */
	public function getAdminIndex($access = 0) {
		return $this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'contain' => array(
				'Forum' => array(
					'conditions' => array('Forum.forum_id' => 0),
					'SubForum' => array(
						'fields' => 'SubForum.*'
					)
				)
			)	
		));
	}
	
	/**
	 * Get the list of forums for the board index.
	 *
	 * @access public
	 * @param int $access
	 * @param array $accessLevels
	 * @return array
	 */
	public function getIndex($access = 0, $accessLevels = array()) {
		return $this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'conditions' => array(
				'Forum.forum_id' => 0,
				'Forum.status' => self::STATUS_OPEN,
				'Forum.accessRead <=' => $access,
				'Forum.access_level_id' => $accessLevels
			),
			'contain' => array(
				'SubForum' => array(
					'conditions' => array(
						'SubForum.accessRead <=' => $access,
						'SubForum.access_level_id' => $accessLevels
					),
					'SubForum' => array(
						'fields' => array('SubForum.id', 'SubForum.title', 'SubForum.slug'),
						'conditions' => array(
							'SubForum.accessRead <=' => $access,
							'SubForum.access_level_id' => $accessLevels
						)
					),
					'LastTopic.title', 'LastTopic.slug', 'LastTopic.created', 'LastTopic.post_count', 'LastPost.created', 'LastUser.username'
				)
			)	
		));
	}
	
	/**
	 * Get a list of forums.
	 * 
	 * @access public
	 * @param int $exclude
	 * @return array
	 */
	public function getList($exclude = null) {
		$conditions = array();
		
		if (is_numeric($exclude)) {
			$conditions['Forum.id !='] = $exclude;
		}
		
		return $this->find('list', array(
			'conditions' => $conditions,
			'order' => array('Forum.orderNo' => 'ASC')
		));
	}
	
	/**
	 * Get all basic info for a forum.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getForum($id) {
		return $this->find('first', array(
			'conditions' => array('Forum.id' => $id),
			'contain' => array('Parent')
		));
	}

	/**
	 * Get all required info for viewing a forum.
	 *
	 * @access public
	 * @param string $slug
	 * @param int $access
	 * @param array $accessLevels
	 * @return array
	 */
	public function getForumForViewing($slug, $access = 0, $accessLevels = array()) {
		return $this->find('first', array(
			'conditions' => array(
				'Forum.slug' => $slug,
				'Forum.access_level_id' => $accessLevels
			),
			'contain' => array(
				'Parent',
				'SubForum' => array(
					'conditions' => array(
						'SubForum.accessRead <=' => $access,
						'SubForum.access_level_id' => $accessLevels
					),
					'LastTopic.title', 'LastTopic.created', 'LastPost.created', 'LastUser.username', 'LastTopic.slug'
				),
				'Moderator' => array('User.id', 'User.username')
			)
		));
	}

	/**
	 * Get the hierarchy.
	 *
	 * @access public
	 * @param int $access
	 * @param array $accessLevels
	 * @param string $type
	 * @param int $exclude
	 * @return array
	 */
	public function getHierarchy($access = 1, $accessLevels = array(), $type = 'post', $exclude = null) {
		/*$accessField = 'access'. ucfirst($type);

		$forums = $this->Forum->find('list', array(
			'conditions' => array(
				'Forum.status' => 0,
				'Forum.accessView <=' => $access,
				'Forum.access_level_id' => $accessLevels
			),
			'order' => array('Forum.orderNo' => 'ASC')
		));

		$conditions = array(
			'Forum.access_level_id' => $accessLevels,
			'Forum.forum_id' => 0
		);

		if (is_numeric($exclude)) {
			$conditions['Forum.id !='] = $exclude;
		}

		$categories = $this->find('all', array(
			'fields' => array('Forum.id', 'Forum.title', 'Forum.slug', 'Forum.forum_id', 'Forum.forum_id', 'Forum.orderNo', 'Forum.'. $accessField),
			'conditions' => $conditions,
			'order' => array('Forum.orderNo' => 'ASC'),
			'contain' => array(
				'Forum.title',
				'SubForum' => array(
					'conditions' => array('SubForum.access_level_id' => $accessLevels),
					'fields' => array('SubForum.id', 'SubForum.title', 'SubForum.'. $accessField)
				)
			)
		));

		if (empty($categories)) {
			return false;
		}

		// Rebuild
		$hierarchy = array_flip($forums);

		foreach ($categories as $category) {
			if (!is_array($hierarchy[$category['Forum']['title']])) {
				$hierarchy[$category['Forum']['title']] = array();
			}

			if ($access >= $category['Forum'][$accessField]) {
				$hierarchy[$category['Forum']['title']][$category['Forum']['id']] = $category['Forum']['title'];

				if (!empty($category['SubForum'])) {
					foreach ($category['SubForum'] as $child) {
						if ($access >= $child[$accessField]) {
							$hierarchy[$category['Forum']['title']][$child['id']] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '. $child['title'];
						}
					}
				}
			}
		}

		foreach ($hierarchy as $key => $value) {
			if (empty($value)) {
				unset($hierarchy[$key]);
			}
		}

		return $hierarchy;*/
	}

	/**
	 * Get a list of parents.
	 *
	 * @access public
	 * @param int $exclude
	 * @return array
	 */
	public function getParents($exclude = null) {
		$conditions = array('Forum.forum_id' => 0);

		if (is_numeric($exclude)) {
			$conditions['Forum.id !='] = $exclude;
		}

		return $this->find('list', array(
			'conditions' => $conditions,
			'order' => array('Forum.orderNo' => 'ASC')
		));
	}

	/**
	 * Increase the post count.
	 *
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increasePosts($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."forums` AS `Forum` SET `Forum`.`post_count` = `Forum`.`post_count` + 1 WHERE `Forum`.`id` = $id");
	}

	/**
	 * Move all categories to a new forum.
	 *
	 * @access public
	 * @param int $start_id
	 * @param int $moved_id
	 * @return boolean
	 */
	public function moveAll($start_id, $moved_id) {
		return $this->updateAll(
			array('Forum.forum_id' => $moved_id),
			array('Forum.forum_id' => $start_id)
		);
	}

	/**
	 * Update the order of the forums.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function updateOrder($data) {
		if (isset($data['_Token'])) {
			unset($data['_Token']);
		}

		if (!empty($data)) {
			foreach ($data as $model => $fields) {
				foreach ($fields as $id => $field) {
					$order = $field['orderNo'];
					if (!is_numeric($order)) {
						$order = 0;
					}

					if ($model == 'Forum') {
						$this->id = $field['id'];
						$this->save(array('orderNo' => $order), false, array('orderNo'));
					} else {
						$this->{$model}->id = $field['id'];
						$this->{$model}->save(array('orderNo' => $order), false, array('orderNo'));
					}
				}
			}
		}

		return true;
	}

}
