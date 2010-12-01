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
		'ForumCategory' => array(
			'className' => 'Forum.ForumCategory',
			'fields' => array('ForumCategory.id', 'ForumCategory.title', 'ForumCategory.slug')
		), 
		'User' => array(
			'className' => 'Forum.User'
		)
	);
	
	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'user_id' => 'notEmpty',
		'forum_category_id' => 'notEmpty'
	);
	
	/**
	 * Return a list of all moderators and their forums.
	 *
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('ForumCategory', 'User'),
			'order' => array('Moderator.forum_category_id' => 'ASC')
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
			'fields' => array('Moderator.forum_category_id')
		));
	}

}
