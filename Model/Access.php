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
			'contain' => array('User'),
			'cache' => __METHOD__
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

}