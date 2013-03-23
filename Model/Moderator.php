<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class Moderator extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Forum' => array(
			'className' => 'Forum.Forum',
			'fields' => array('Forum.id', 'Forum.title', 'Forum.slug')
		),
		'User' => array(
			'className' => USER_MODEL
		)
	);

	/**
	 * Validation.
	 *
	 * @var array
	 */
	public $validations = array(
		'default' => array(
			'user_id' => array(
				'rule' => 'notEmpty'
			),
			'forum_id' => array(
				'rule' => 'notEmpty'
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-legal'
	);

	/**
	 * Add a moderator after validating conditions.
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add($data) {
		if ($user = $this->validate($data)) {
			$this->create();
			$this->save($data, false);

			return $user;
		}

		return false;
	}

	/**
	 * Edit a moderator after validating conditions.
	 *
	 * @param int $id
	 * @param array $data
	 * @return bool
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
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Moderator.id' => $id),
			'contain' => array('User', 'Forum'),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Return a list of all moderators and their forums.
	 *
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'contain' => array('Forum', 'User'),
			'order' => array('Moderator.forum_id' => 'ASC'),
			'cache' => __METHOD__
		));
	}

	/**
	 * Return a list of all forums a user moderates.
	 *
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
	 * @param array $data
	 * @return bool
	 */
	public function validate($data) {
		$this->set($data);

		if ($this->validates()) {
			$user = $this->User->find('first', array(
				'conditions' => array('User.id' => $data['user_id'])
			));

			if (!$user) {
				return $this->invalidate('user_id', 'No user exists with this ID');
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

			return $user;
		}

		return false;
	}

}
