<?php
/**
 * Forum - Access
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class Access extends ForumAppModel {

	/**
	 * Access IDs.
	 */
	const GUEST = 0;
	const MEMBER = 1;
	const MOD = 2;
	const SUPER = 3;
	const ADMIN = 4;

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
		'User' => array(
			'className' => FORUM_USER
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
		'access_level_id' => 'notEmpty'
	);

	/**
	 * Add a user once conditions are validated.
	 *
	 * @access public
	 * @param array $data
	 * @return mixed
	 */
	public function add($data) {
		if ($user = $this->validate($data)) {
			if ($this->grant($data['user_id'], $data['access_level_id'])) {
				return $user;
			}
		}

		return false;
	}

	/**
	 * Grant access to a user, validating conditions.
	 *
	 * @access public
	 * @param int $user_id
	 * @param int $level_id
	 * @return boolean
	 */
	public function grant($user_id, $level_id) {
		$count = $this->find('count', array(
			'conditions' => array(
				'Access.user_id' => $user_id,
				'Access.access_level_id' => $level_id
			)
		));

		if ($count) {
			return $this->invalidate('user_id', 'User already has this access');
		}

		$this->create();

		return $this->save(array(
			'user_id' => $user_id,
			'access_level_id' => $level_id
		));
	}

	/**
	 * Return an access level and its user.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Access.id' => $id),
			'contain' => array('User', 'AccessLevel'),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Get a list of all staff and their levels.
	 *
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('User' => array('Profile'), 'AccessLevel'),
			'order' => array('Access.access_level_id' => 'ASC'),
			'cache' => __METHOD__
		));
	}

	/**
	 * Get a list of all levels for a user.
	 *
	 * @access public
	 * @param int $user_id
	 * @return array
	 */
	public function getListByUser($user_id) {
		return $this->find('all', array(
			'contain' => array('AccessLevel'),
			'conditions' => array('Access.user_id' => $user_id)
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

	/**
	 * Validate logical conditions.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function validate($data) {
		$this->set($data);

		if ($this->validates()) {
			$userCount = $this->User->find('count', array(
				'conditions' => array('User.id' => $data['user_id'])
			));

			if ($userCount <= 0) {
				return $this->invalidate('user_id', 'No user exists with this ID');
			}

			return ClassRegistry::init('Profile')->getUserProfile($data['user_id']);
		}

		return false;
	}

}
