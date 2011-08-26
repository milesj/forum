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
			'className'		=> 'User',
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
		'Children' => array(
			'className' 	=> 'Forum.Forum',
			'foreignKey' 	=> 'forum_id',
			'order' 		=> array('Children.orderNo' => 'ASC'),
			'dependent'		=> false
		),
		'SubForum' => array(
			'className' 	=> 'Forum.Forum',
			'foreignKey' 	=> 'forum_id',
			'order' 		=> array('SubForum.orderNo' => 'ASC'),
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
	 * Update all forums by going up the parent chain.
	 * 
	 * @access public
	 * @param int $id
	 * @param array $data 
	 * @return void
	 */
	public function chainUpdate($id, array $data) {
		$this->id = $id;
		$this->save($data, false, array_keys($data));
		
		$forum = $this->getById($id);
		
		if ($forum['Forum']['forum_id'] != 0) {
			$this->chainUpdate($forum['Forum']['forum_id'], $data);
		}
	}
	
	/**
	 * Get a forum.
	 *
	 * @access public
	 * @param string $slug
	 * @return array
	 */
	public function get($slug) {
		$access = $this->access();
		$accessLevels = $this->accessLevels();
		
		return $this->find('first', array(
			'conditions' => array(
				'Forum.access_level_id' => $accessLevels,
				'Forum.accessRead <=' => $access,
				'Forum.slug' => $slug
			),
			'contain' => array(
				'Parent', 
				'SubForum' => array(
					'conditions' => array(
						'SubForum.access_level_id' => $accessLevels,
						'SubForum.accessRead <=' => $access
					),
					'LastTopic', 'LastPost', 'LastUser'
				),
				'Moderator' => array('User')
			),
			'cache' => __FUNCTION__ .'-'. $slug
		));
	}
	
	/**
	 * Return a forum based on ID.
	 * 
	 * @acccess public
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Forum.id' => $id)
		));
	}
		
	/**
	 * Get the list of forums for the board index.
	 *
	 * @access public
	 * @return array
	 */
	public function getAdminIndex() {
		return $this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'conditions' => array('Forum.forum_id' => 0),
			'contain' => array('Children' => array('SubForum'))
		));
	}
	
	/**
	 * Get a grouped hierarchy.
	 *
	 * @access public
	 * @param string $type
	 * @param int $exclude
	 * @return array
	 */
	public function getGroupedHierarchy($type, $exclude = null) {
		$conditions = array(
			'Forum.status' => self::STATUS_OPEN,
			'Forum.'. $type .' <=' => $this->access(),
			'Forum.access_level_id' => $this->accessLevels()
		);
		
		if (is_numeric($exclude)) {
			$conditions['Forum.id !='] = $exclude;
		}
		
		$forums = $this->find('all', array(
			'fields' => array('Forum.id', 'Forum.title', 'Forum.forum_id', 'Forum.orderNo'),
			'conditions' => $conditions,
			'order' => array('Forum.orderNo' => 'ASC'),
			'contain' => false
		));

		$root = array();
		$categories = array();
		$hierarchy = array();
		
		foreach ($forums as $forum) {
			if ($forum['Forum']['forum_id'] == 0) {
				$root[] = $forum['Forum'];
			} else {
				$categories[$forum['Forum']['forum_id']][$forum['Forum']['orderNo']] = $forum['Forum'];
			}
		}
		
		foreach ($root as $forum) {
			if (isset($categories[$forum['id']])) {
				$hierarchy[$forum['title']] = $this->_buildOptions($categories, $forum);
			}
		}

		return $hierarchy;
	}

	/**
	 * Get the hierarchy.
	 *
	 * @access public
	 * @param boolean $drill
	 * @param int $exclude
	 * @return array
	 */
	public function getHierarchy($drill = false, $exclude = null) {
		$conditions = array();
		
		if (is_numeric($exclude)) {
			$conditions = array(
				'Forum.id !=' => $exclude,
				'Forum.forum_id !=' => $exclude
			);
		}
		
		$forums = $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array('Forum.id', 'Forum.title', 'Forum.forum_id'),
			'order' => array('Forum.orderNo' => 'ASC'),
			'contain' => false
		));

		$root = array();
		$categories = array();
		$hierarchy = array();
		
		foreach ($forums as $forum) {
			if ($forum['Forum']['forum_id'] == 0) {
				$root[] = $forum['Forum'];
			} else {
				$categories[$forum['Forum']['forum_id']][] = $forum['Forum'];
			}
		}
		
		foreach ($root as $forum) {
			$hierarchy[$forum['id']] = $forum['title'];
			$hierarchy += $this->_buildOptions($categories, $forum, $drill, 1);
		}

		return $hierarchy;
	}

	/**
	 * Get the list of forums for the board index.
	 *
	 * @access public
	 * @return array
	 */
	public function getIndex() {
		$access = $this->access();
		$accessLevels = $this->accessLevels();
		
		return $this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'conditions' => array(
				'Forum.forum_id' => 0,
				'Forum.status' => self::STATUS_OPEN,
				'Forum.accessRead <=' => $access,
				'Forum.access_level_id' => $accessLevels
			),
			'contain' => array(
				'Children' => array(
					'conditions' => array(
						'Children.accessRead <=' => $access,
						'Children.access_level_id' => $accessLevels
					),
					'SubForum' => array(
						'fields' => array('SubForum.id', 'SubForum.title', 'SubForum.slug'),
						'conditions' => array(
							'SubForum.accessRead <=' => $access,
							'SubForum.access_level_id' => $accessLevels
						)
					),
					'LastTopic', 'LastPost', 'LastUser'
				)
			),
			'cache' => __FUNCTION__
		));
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
					
					$this->id = $field['id'];
					$this->save(array('orderNo' => $order), false, array('orderNo'));
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Build the list of select options.
	 * 
	 * @access protected
	 * @param array $categories
	 * @param array $forum
	 * @param boolean $drill
	 * @param int $depth
	 * @return array 
	 */
	protected function _buildOptions($categories, $forum, $drill = true, $depth = 0) {
		$options = array();
		
		if (isset($categories[$forum['id']])) {
			$children = $categories[$forum['id']];
			ksort($children);

			foreach ($children as $child) {
				$options[$child['id']] = str_repeat('&nbsp;', ($depth * 4)) . $child['title'];
				
				if (isset($categories[$child['id']]) && $drill) {
					$babies = $this->_buildOptions($categories, $child, $drill, ($depth + 1));
					
					if (!empty($babies)) {
						$options = $options + $babies;
					}
				}
			}
		}
		
		return $options;
	}
	
}
