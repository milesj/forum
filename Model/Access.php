<?php

App::uses('Aro', 'Model');

class Access extends Aro {

	/**
	 * No recursion.
	 *
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Force model alias.
	 *
	 * @var string
	 */
	public $alias = 'Access';

	/**
	 * Use AROs table.
	 *
	 * @var string
	 */
	public $useTable = 'aros';

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Forum.Access',
			'foreignKey' => 'parent_id'
		),
		'User' => array(
			'className' => FORUM_USER,
			'foreignKey' => 'foreign_key',
			'conditions' => array('Access.model' => 'User')
		)
	);

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Containable',
		'Utility.Cacheable' => array(
			'cacheConfig' => 'forum',
			'appendKey' => false
		)
	);

	/**
	 * Validation.
	 *
	 * @var array
	 */
	public $validate = array(
		'foreign_key' => 'notEmpty',
		'parent_id' => 'notEmpty'
	);

	/**
	 * Add a user once conditions are validated.
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function add($data) {
		$this->set($data);

		if ($this->validates()) {
			$exists = $this->User->find('count', array(
				'conditions' => array('User.id' => $data['foreign_key'])
			));

			if ($exists <= 0) {
				$this->invalidate('foreign_key', __d('forum', 'No user exists with this ID'));
				return false;
			}

			$aro = $this->getByUserId($data['foreign_key']);

			if (!empty($aro) && $aro['Access']['parent_id'] == $data['parent_id']) {
				$this->invalidate('foreign_key', __d('forum', 'User already has this access'));
				return false;
			}

			$user = ClassRegistry::init('Forum.Profile')->getUserProfile($data['foreign_key']);

			$this->create();
			$this->save(array(
				'alias' => $user['User'][Configure::read('Forum.userMap.username')],
				'parent_id' => $data['parent_id'],
				'model' => 'User',
				'foreign_key' => $data['foreign_key']
			));

			return $user;
		}

		return false;
	}

	/**
	 * Return all records.
	 *
	 * @return array
	 */
	public function getAll() {
		return $this->find('all', array(
			'conditions' => array('Access.alias LIKE' => 'forum.%', 'Access.parent_id' => null),
			'cache' => __METHOD__
		));
	}

	/**
	 * Return all records as a list.
	 *
	 * @return array
	 */
	public function getList() {
		return $this->find('list', array(
			'conditions' => array('Access.alias LIKE' => 'forum.%', 'Access.parent_id' => null),
			'fields' => array('Access.id', 'Access.alias'),
			'cache' => __METHOD__
		));
	}

	/**
	 * Return a record based on ID.
	 *
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Access.id' => $id),
			'contain' => array('Group', 'User'),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Return a record based on user ID.
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function getByUserId($user_id) {
		return $this->find('first', array(
			'conditions' => array(
				'Access.foreign_key' => $user_id,
				'Access.model' => 'User'
			),
			'contain' => array('Group', 'User'),
			'cache' => array(__METHOD__, $user_id)
		));
	}

	/**
	 * Return a record based on slug.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function getBySlug($slug) {
		if (substr($slug, 0, 6) !== 'forum.') {
			$slug = 'forum.' . $slug;
		}

		return $this->find('first', array(
			'conditions' => array('Access.alias' => $slug),
			'cache' => array(__METHOD__, $slug)
		));
	}

	/**
	 * Return all the staff.
	 *
	 * @return array
	 */
	public function getStaff() {
		return $this->find('all', array(
			'conditions' => array('Access.parent_id' => array_keys($this->getList())),
			'contain' => array('User' => array('ForumProfile'), 'Group')
		));
	}

	/**
	 * Return all the staff by slug.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function getStaffBySlug($slug) {
		$access = $this->getBySlug($slug);

		return $this->find('all', array(
			'conditions' => array('Access.parent_id' => $access['Access']['id']),
			'contain' => array('User'),
			'cache' => array(__METHOD__, $slug)
		));
	}

	/**
	 * Return all the administrators.
	 *
	 * @return array
	 */
	public function getAdmins() {
		return $this->getStaffBySlug('admin');
	}

	/**
	 * Return all the super moderators.
	 *
	 * @return array
	 */
	public function getSuperMods() {
		return $this->getStaffBySlug('superMod');
	}

	/**
	 * Return a list of users permissions. Include parent groups permissions also.
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function getPermissions($user_id) {
		try {
			$aros = $this->node(array('User' => array('id' => $user_id)));
		} catch (Exception $e) {
			return null;
		}

		return ClassRegistry::init('Permission')->find('all', array(
			'conditions' => array('Permission.aro_id' => Hash::extract($aros, '{n}.Access.id')),
			'order' => array('Aco.lft' => 'desc'),
			'recursive' => 0
		));
	}

}