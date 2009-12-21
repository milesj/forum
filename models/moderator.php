<?php
/** 
 * moderator.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Moderator Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class Moderator extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
	 * @var array 
	 */
	public $belongsTo = array(
		'ForumCategory' => array(
			'className' => 'Forum.ForumCategory',
			'fields' => array('ForumCategory.id', 'ForumCategory.title')
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
		'forum_category_id' => 'notEmpty'
	);
	
	/**
	 * Return a list of all moderators and their forums
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('ForumCategory', 'User'),
			'order' => 'Moderator.forum_category_id ASC'
		));
	}
	
	/**
	 * Get all forums you moderate
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
