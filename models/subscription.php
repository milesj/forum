<?php
/** 
 * Forum - Subscription
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */
 
class Subscription extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'User',
		'Forum' => array(
			'className'		=> 'Forum.Forum',
			'foreignKey'	=> 'forum_id'
		),
		'Topic' => array(
			'className' 	=> 'Forum.Topic',
			'foreignKey'	=> 'topic_id'
		)
	);
	
	/**
	 * Determine if the user is already subscribed to a topic?
	 * 
	 * @access public
	 * @param int $user_id
	 * @param int $topic_id
	 * @return int
	 */
	public function isSubscribed($user_id, $topic_id) {
		return $this->find('first', array(
			'conditions' => array(
				'Subscription.user_id' => $user_id,
				'Subscription.topic_id' => $topic_id
			)
		));
	}
	
	/**
	 * Subscribe a user to a topic.
	 * 
	 * @access public
	 * @param int $user_id
	 * @param int $topic_id
	 * @return boolean
	 */
	public function subscribe($user_id, $topic_id) {
		$topic = $this->Topic->getById($topic_id);
		
		if (empty($topic) || $this->isSubscribed($user_id, $topic_id)) {
			return false;
		}
		
		$this->create();
		
		return $this->save(array(
			'user_id' => $user_id,
			'topic_id' => $topic_id,
			'forum_id' => $topic['Topic']['forum_id']
		), false);
	}
	
	/**
	 * Unsubscribe a user from a topic.
	 * 
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function unsubscribe($id) {
		return $this->delete($id, true);
	}
	
}