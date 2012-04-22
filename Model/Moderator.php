<?php
/**
 * Forum - Moderator
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
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
	 * Add a moderator after validating conditions.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function add($data) {
		if ($this->validate($data)) {
			$this->create();
			return $this->save($data, false);
		}

		return false;
	}

	/**
	 * Edit a moderator after validating conditions.
	 *
	 * @access public
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function edit($id, $data) {
		if ($this->validate($data)) {
			$this->id = $id;
			return $this->save($data, false, array('forum_id'));
		}

		return false;
	}

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
			'contain' => array('Forum', 'User' => array('Profile')),
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
			if (!empty($data['user_id'])) {
				$userCount = $this->User->find('count', array(
					'conditions' => array('User.id' => $data['user_id'])
				));

				if ($userCount <= 0) {
					return $this->invalidate('user_id', 'No user exists with this ID');
				}
			}

			$forumCount = $this->find('count', array(
				'conditions' => array(
					'Moderator.user_id' => $data['user_id'],
					'Moderator.forum_id' => $data['forum_id']
				)
			));

			if ($forumCount >= 1) {
				return $this->invalidate('user_id', 'This user is already a moderator for this forum');
			}

			return true;
		}

		return false;
	}

}
