<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

class ForumUser extends ForumAppModel {

	/**
	 * Force the model to act like the User model.
	 *
	 * @type string
	 */
	public $name = USER_MODEL;
	public $alias = USER_MODEL;

	/**
	 * No prefix on users table.
	 *
	 * @type string
	 */
	public $tablePrefix = null;

	/**
	 * Disable admin.
	 *
	 * @type bool
	 */
	public $admin = false;

	/**
	 * Get the newest signup.
	 *
	 * @return array
	 */
	public function getNewestUser() {
		return $this->find('first', array(
			'order' => array('User.created' => 'DESC'),
			'limit' => 1,
			'cache' => __METHOD__
		));
	}

	/**
	 * Increase the post count.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function increasePosts($user_id) {
		$field = Configure::read('User.fieldMap.totalPosts');

		if (!$this->hasField($field)) {
			return false;
		}

		return $this->updateAll(
			array('User.' . $field => 'User.' . $field . ' + 1'),
			array('User.id' => $user_id)
		);
	}

	/**
	 * Increase the topic count.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function increaseTopics($user_id) {
		$field = Configure::read('User.fieldMap.totalTopics');

		if (!$this->hasField($field)) {
			return false;
		}

		return $this->updateAll(
			array('User.' . $field => 'User.' . $field . ' + 1'),
			array('User.id' => $user_id)
		);
	}

	/**
	 * Get who's online within the past x minutes.
	 *
	 * @return array
	 */
	public function whosOnline() {
		$minutes = Configure::read('Forum.settings.whosOnlineInterval');
		$currentLogin = Configure::read('User.fieldMap.currentLogin');

		if (!$currentLogin) {
			return null;
		}

		return $this->find('all', array(
			'conditions' => array('User.' . $currentLogin . ' >' => date('Y-m-d H:i:s', strtotime($minutes))),
			'cache' => array(__METHOD__, $minutes),
			'cacheExpires' => '+15 minutes'
		));
	}

}