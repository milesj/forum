<?php
/** 
 * Forum - Moderator Model
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class Moderator extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array 
	 */
	public $belongsTo = array(
		'Forum' => array(
			'className' => 'Forum.Forum',
			'fields' => array('Forum.id', 'Forum.title', 'Forum.slug')
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
		'forum_id' => 'notEmpty'
	);
		
	/**
	 * Return an moderator and their forum.
	 * 
	 * @access public
	 * @param int $id
	 * @return array 
	 */
	public function get($id) {
		return $this->find('first', array(
			'conditions' => array('Moderator.id' => $id),
			'contain' => array('User', 'Forum')
		));
	}
	
	/**
	 * Return a list of all moderators and their forums.
	 *
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('Forum', 'User'),
			'order' => array('Moderator.forum_id' => 'ASC')
		));
	}
	
	/**
	 * Return a list of all forums a user moderates.
	 *
	 * @access public
	 * @param int $user_id
	 * @return array
	 */
	public function getListByUser($user_id) {
		return $this->find('all', array(
			'contain' => array('Forum'),
			'conditions' => array('Moderator.user_id' => $user_id)
		));
	}
	
	/**
	 * Get all forums you moderate.
	 * 
	 * @access public
	 * @param int $user_id
	 * @return array
	 */
	public function getModerations($user_id) {
		return $this->find('list', array(
			'conditions' => array('Moderator.user_id' => $user_id),
			'fields' => array('Moderator.forum_id')
		));
	}

}
