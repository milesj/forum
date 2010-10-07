<?php
/**
 * Forum - Profile Model
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class Profile extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array('User');

	/**
	 * Get the newest signup.
	 *
	 * @access public
	 * @return array
	 */
	public function getNewestUser() {
		return $this->find('first', array(
			'order' => array('Profile.created' => 'DESC'),
			'contain' => array('User'),
			'limit' => 1
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
		return $this->query("UPDATE `". $this->tablePrefix ."profiles` AS `Profile` SET `Profile`.`totalPosts` = `Profile`.`totalPosts` + 1 WHERE `Profile`.`id` = $id");
	}

	/**
	 * Increase the topic count.
	 *
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increaseTopics($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."profiles` AS `Profile` SET `Profile`.`totalTopics` = `Profile`.`totalTopics` + 1 WHERE `Profile`.`id` = $id");
	}
	
	/**
	 * Login the user and update records.
	 *
	 * @access public
	 * @param array $user
	 * @return boolean
	 */
	public function login($user) {
		if (!empty($user)) {
			$this->id = $user['User']['id'];

			return $this->save(array(
				'currentLogin' => date('Y-m-d H:i:s'),
				'lastLogin' => $user['User']['currentLogin']
			), false);
		}

		return true;
	}

	/**
	 * Get whos online within the past x minutes.
	 *
	 * @access public
	 * @param int $minutes
	 * @return array
	 */
	public function whosOnline($minutes) {
		return $this->find('all', array(
			'conditions' => array('Profile.currentLogin >' => date('Y-m-d H:i:s', strtotime('-'. $minutes .' minutes'))),
			'contain' => array('User')
		));
	}

}