<?php
/** 
 * Forum - Access Model
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class Access extends ForumAppModel {

	/**
	 * DB Table.
	 *
	 * @access public
	 * @var string
	 */
	public $useTable = 'access';

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array 
	 */
	public $belongsTo = array(
		'AccessLevel' => array(
			'className' => 'Forum.AccessLevel'
		), 
		'User'
	);
	
	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'user_id' => 'notEmpty',
		'access_level_id' => 'notEmpty'
	);
	
	/**
	 * Get a list of all staff and their levels.
	 *
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('User', 'AccessLevel'),
			'order' => array('Access.access_level_id' => 'ASC')
		));
	}
	
	/**
	 * Get all my access.
	 *
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
	 * Check to see if the user has the admin role.
	 *
	 * @access public
	 * @param int $user_id
	 * @return int
	 */
	public function isAdmin($user_id) {
		return $this->find('count', array(
			'conditions' => array(
				'Access.user_id' => $user_id,
				'AccessLevel.isAdmin' => self::BOOL_YES
			),
			'contain' => array('AccessLevel.isAdmin')
		));	
	}
	
	/**
	 * Check to see if the user has the super moderator role.
	 *
	 * @access public
	 * @param int $user_id
	 * @return int
	 */
	public function isSuper($user_id) {
		return $this->find('count', array(
			'conditions' => array(
				'Access.user_id' => $user_id,
				'AccessLevel.isSuper' => self::BOOL_YES
			),
			'contain' => array('AccessLevel.isSuper')
		));	
	}
	
	/**
	 * Move all users to a new level.
	 * 
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
