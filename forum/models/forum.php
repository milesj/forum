<?php
/** 
 * forum.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Forum Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class Forum extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'AccessLevel' => array(
			'className' => 'Forum.AccessLevel'
		)
	);

	/**
	 * Has many
	 * @access public
	 * @var array 
	 */
	public $hasMany = array(
		'ForumCategory' => array(
			'className' 	=> 'Forum.ForumCategory',
			'conditions' 	=> array('ForumCategory.parent_id' => 0),
			'order'			=> 'ForumCategory.orderNo ASC',
			'dependent'		=> false
		)
	); 
	
	/**
	 * Validate
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'title' => 'notEmpty',
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
	 * Get the list of forums for the board index
	 * @access public
	 * @param int $access
	 * @return array
	 */
	public function getAdminIndex($access = 0) {
		return $this->find('all', array(
			'order' => 'Forum.orderNo ASC',
			'contain' => array(
				'ForumCategory' => array(
					'conditions' => array('ForumCategory.parent_id' => 0),
					'SubForum' => array(
						'fields' => 'SubForum.*'
					)
				)
			)	
		));
	}
	
	/**
	 * Get the list of forums for the board index
	 * @access public
	 * @param int $access
	 * @param array $accessLevels
	 * @return array
	 */
	public function getIndex($access = 0, $accessLevels = array()) {
		return $this->find('all', array(
			'order' => 'Forum.orderNo ASC',
			'conditions' => array(
				'Forum.status' => 0,
				'Forum.accessView <=' => $access,
				'Forum.access_level_id' => $accessLevels
			),
			'contain' => array(
				'ForumCategory' => array(
					'fields' => array('ForumCategory.id', 'ForumCategory.forum_id', 'ForumCategory.parent_id', 'ForumCategory.title', 'ForumCategory.description', 'ForumCategory.status', 'ForumCategory.topic_count', 'ForumCategory.post_count'),
					'conditions' => array(
						'ForumCategory.access_level_id' => $accessLevels,
						'ForumCategory.accessRead <=' => $access,
						'ForumCategory.parent_id' => 0
					),
					'SubForum' => array(
						'fields' => array('SubForum.id', 'SubForum.title'), 
						'conditions' => array(
							'SubForum.accessRead <=' => $access,
							'SubForum.access_level_id' => $accessLevels
						)
					),
					'LastTopic.title', 'LastTopic.created', 'LastTopic.post_count', 'LastPost.created', 'LastUser.username'
				)
			)	
		));
	}
	
	/**
	 * Get a list of forums
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
			'order' => 'Forum.orderNo ASC'
		));
	}

}
