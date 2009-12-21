<?php
/** 
 * access.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Access Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class Access extends ForumAppModel {

	/**
	 * DB Table
	 * @access public
	 * @var string
	 */
	public $useTable = 'access';

	/**
	 * Belongs to
	 * @access public
	 * @var array 
	 */
	public $belongsTo = array(
		'AccessLevel' => array(
			'className' => 'Forum.AccessLevel'
		), 
		'User' => array(
			'className' => 'Forum.User'
		)
	);
	
	/**
	 * Validation
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'user_id' => 'notEmpty',
		'access_level_id' => 'notEmpty'
	);
	
	/**
	 * Get a list of all staff and their levels
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('User', 'AccessLevel'),
			'order' => 'Access.access_level_id ASC'
		));
	}
	
	/**
	 * Get all my access
	 * @access public
	 * @param int $user_id
	 * @return array
	 */
	public function getMyAccess($user_id) {
		$levels = $this->find('all', array(
			'conditions' => array('Access.user_id' => $user_id),
			'contain' => array('AccessLevel')
		));
		
		$clean = array();
		if (!empty($levels)) {
			foreach ($levels as $level) {
				$clean[$level['AccessLevel']['title']] = $level['AccessLevel']['level'];
			}
		}
		
		return $clean;
	}
	
	/**
	 * Check to see if the user has the admin role
	 * @access public
	 * @param int $user_id
	 * @return int
	 */
	public function isAdmin($user_id) {
		return $this->find('count', array(
			'conditions' => array(
				'Access.user_id' => $user_id,
				'AccessLevel.is_admin' => 1
			),
			'contain' => array('AccessLevel.is_admin')
		));	
	}
	
	/**
	 * Check to see if the user has the super moderator role
	 * @access public
	 * @param int $user_id
	 * @return int
	 */
	public function isSuper($user_id) {
		return $this->find('count', array(
			'conditions' => array(
				'Access.user_id' => $user_id,
				'AccessLevel.is_super' => 1
			),
			'contain' => array('AccessLevel.is_super')
		));	
	}
	
	/**
	 * Move all users to a new level
	 * @access public
	 * @param int $start_id
	 * @param int $moved_id
	 * @return boolean
	 */
	public function moveAll($start_id, $moved_id) {
		return $this->updateAll(
			array('Access.access_level_id' => $moved_id),
			array('Access.access_level_id' => $start_id)
		);
	}
	
}
