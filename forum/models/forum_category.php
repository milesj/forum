<?php
/** 
 * forum_category.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - ForumCategory Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum 
 */
 
class ForumCategory extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
	 * @var array
	 */
	public $belongsTo = array( 
		'Forum' => array(
			'className'		=> 'Forum.Forum'
		),
		'Parent' => array(
			'className'		=> 'Forum.ForumCategory',
			'foreignKey'	=> 'parent_id',
			'fields'		=> array('Parent.id', 'Parent.title', 'Parent.parent_id')
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
	 * Has many
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'Topic' => array(
			'className'		=> 'Forum.Topic',
			'dependent'		=> false
		),
		'SubForum' => array(
			'className' 	=> 'Forum.ForumCategory',
			'foreignKey' 	=> 'parent_id',
			'order' 		=> 'SubForum.orderNo ASC',
			'fields' 		=> array('SubForum.id', 'SubForum.forum_id', 'SubForum.parent_id', 'SubForum.title', 'SubForum.description', 'SubForum.status', 'SubForum.topic_count', 'SubForum.post_count', 'SubForum.lastTopic_id', 'SubForum.lastPost_id', 'SubForum.lastUser_id'),
			'dependent'		=> false
		),
		'Moderator' => array(
			'className'		=> 'Forum.Moderator',
			'dependent'		=> true,
			'exclusive'		=> true
		)
	);
	
	/**
	 * Validate
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
	 * Get all basic info for a category
	 * @access public
	 * @param int $id
	 * @param int $access
	 * @return array
	 */
	public function getCategory($id) {
		return $this->find('first', array(
			'conditions' => array('ForumCategory.id' => $id),
			'contain' => array('Forum', 'Parent')
		));
	}
	
	/**
	 * Get all required info for viewing a category
	 * @access public
	 * @param int $id
	 * @param int $access
	 * @param array $accessLevels
	 * @return array
	 */
	public function getCategoryForViewing($id, $access = 0, $accessLevels) {
		return $this->find('first', array(
			'conditions' => array(
				'ForumCategory.id' => $id,
				'ForumCategory.access_level_id' => $accessLevels
			),
			'contain' => array(
				'Forum', 'Parent', 
				'SubForum' => array(
					'conditions' => array(
						'SubForum.accessRead <=' => $access,
						'SubForum.access_level_id' => $accessLevels
					),
					'LastTopic.title', 'LastTopic.created', 'LastPost.created', 'LastUser.username'
				),
				'Moderator' => array('User.id', 'User.username')
			)
		));
	}
	
	/**
	 * Get the hierarchy
	 * @access public
	 * @param int $access
	 * @param array $accessLevels
	 * @param string $type
	 * @param int $exclude
	 * @return array
	 */
	public function getHierarchy($access = 1, $accessLevels, $type = 'post', $exclude = null) {
		$accessField = 'access'. ucfirst($type);
		
		$forums = $this->Forum->find('list', array(
			'conditions' => array(
				'Forum.status' => 0,
				'Forum.accessView <=' => $access,
				'Forum.access_level_id' => $accessLevels
			),
			'order' => 'Forum.orderNo ASC'
		));
		
		$conditions = array(
			'ForumCategory.access_level_id' => $accessLevels,
			'ForumCategory.parent_id' => 0
		);
		if (is_numeric($exclude)) {
			$conditions['ForumCategory.id !='] = $exclude;
		}

		$categories = $this->find('all', array(
			'fields' => array('ForumCategory.id', 'ForumCategory.title', 'ForumCategory.parent_id', 'ForumCategory.forum_id', 'ForumCategory.orderNo', 'ForumCategory.'. $accessField),
			'conditions' => $conditions,
			'order' => 'ForumCategory.orderNo ASC',
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
			
			if ($access >= $category['ForumCategory'][$accessField]) {
				$hierarchy[$category['Forum']['title']][$category['ForumCategory']['id']] = $category['ForumCategory']['title'];
			}
			
			if (!empty($category['SubForum'])) {
				foreach ($category['SubForum'] as $child) {
					if ($access >= $child[$accessField]) {
						$hierarchy[$category['Forum']['title']][$child['id']] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '. $child['title'];
					}
				}
			}
		}
		
		return $hierarchy;
	}
	
	/**
	 * Get a list of parents
	 * @access public
	 * @param int $exclude
	 * @return array
	 */
	public function getParents($exclude = null) {
		$conditions = array('ForumCategory.parent_id' => 0);
		if (is_numeric($exclude)) {
			$conditions['ForumCategory.id !='] = $exclude;
		}
		
		return $this->find('list', array(
			'conditions' => $conditions,
			'order' => 'ForumCategory.orderNo ASC'
		));
	}
	
	/**
	 * Increase the post count
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increasePosts($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."forum_categories` AS `ForumCategory` SET `ForumCategory`.`post_count` = `ForumCategory`.`post_count` + 1 WHERE `ForumCategory`.`id` = $id");
	}
	
	/**
	 * Move all categories to a new forum
	 * @access public
	 * @param int $start_id
	 * @param int $moved_id
	 * @param boolean $parent
	 * @return boolean
	 */
	public function moveAll($start_id, $moved_id, $parent = false) {
		$field = ($parent) ? 'parent_id' : 'forum_id';
		
		return $this->updateAll(
			array('ForumCategory.'. $field => $moved_id),
			array('ForumCategory.'. $field => $start_id)
		);
	}
	
	/**
	 * Update the order of the forums
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
					
					if ($model == 'ForumCategory') {
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
