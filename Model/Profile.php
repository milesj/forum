<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class Profile extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => FORUM_USER
		)
	);

	/**
	 * Validate.
	 *
	 * @var array
	 */
	public $validate = array(
		'totalPosts' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field is required'
			)
		),
		'totalTopics' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field is required'
			)
		)
	);

	/**
	 * Grab a profile based on ID.
	 *
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Profile.id' => $id),
			'contain' => array('User'),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Get a users profile and all relevant information.
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function getByUser($user_id) {
		return $this->find('first', array(
			'conditions' => array('Profile.user_id' => $user_id),
			'contain' => array(
				'User' => array(
					'Moderator' => array('Forum.id', 'Forum.slug', 'Forum.title')
				)
			)
		));
	}

	/**
	 * Return the latest user profiles.
	 *
	 * @param int $limit
	 * @return int
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'order' => array('Profile.created' => 'DESC'),
			'contain' => array('User'),
			'limit' => $limit,
			'cache' => array(__METHOD__, $limit)
		));
	}

	/**
	 * Get the newest signup.
	 *
	 * @return array
	 */
	public function getNewestUser() {
		return $this->find('first', array(
			'order' => array('Profile.created' => 'DESC'),
			'contain' => array('User'),
			'limit' => 1,
			'cache' => __METHOD__
		));
	}

	/**
	 * Grab the users profile. If it doesn't exist, create it!
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function getUserProfile($user_id) {
		$profile = $this->find('first', array(
			'conditions' => array('Profile.user_id' => $user_id),
			'contain' => array('User')
		));

		if (!$profile && $user_id) {
			$this->create();
			$this->save(array('user_id' => $user_id), false);

			return $this->find('first', array(
				'conditions' => array('Profile.id' => $this->id),
				'contain' => array('User')
			));
		}

		return $profile;
	}

	/**
	 * Increase the post count.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function increasePosts($user_id) {
		return $this->query('UPDATE `' . $this->tablePrefix . 'profiles` AS `Profile` SET `Profile`.`totalPosts` = `Profile`.`totalPosts` + 1 WHERE `Profile`.`user_id` = ' . (int) $user_id);
	}

	/**
	 * Increase the topic count.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function increaseTopics($user_id) {
		return $this->query('UPDATE `' . $this->tablePrefix . 'profiles` AS `Profile` SET `Profile`.`totalTopics` = `Profile`.`totalTopics` + 1 WHERE `Profile`.`user_id` = ' . (int) $user_id);
	}

	/**
	 * Login the user and update records.
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function login($user_id) {
		if ($profile = $this->getUserProfile($user_id)) {
			$this->id = $profile['Profile']['id'];

			return $this->save(array(
				'currentLogin' => date('Y-m-d H:i:s'),
				'lastLogin' => $profile['Profile']['currentLogin']
			), false);
		}

		return false;
	}

	/**
	 * Get whos online within the past x minutes.
	 *
	 * @param int $minutes
	 * @return array
	 */
	public function whosOnline($minutes = null) {
		if (!$minutes) {
			$minutes = Configure::read('Forum.settings.whosOnlineInterval');
		}

		return $this->find('all', array(
			'conditions' => array('Profile.currentLogin >' => date('Y-m-d H:i:s', strtotime($minutes))),
			'contain' => array('User'),
			'cache' => array(__METHOD__, $minutes),
			'cacheExpires' => '+15 minutes'
		));
	}

	/**
	 * Parse the HTML version.
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = array()) {
		return $this->validateDecoda('Profile', 'signature');
	}

}