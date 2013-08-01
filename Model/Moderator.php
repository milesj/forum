<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property Forum $Forum
 * @property User $User
 */
class Moderator extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @type array
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
	 * @type array
	 */
	public $validations = array(
		'default' => array(
			'user_id' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty'
				),
				'checkUniqueMod' => array(
					'rule' => 'checkUniqueMod',
					'message' => 'This user is already moderating this forum'
				)
			),
			'forum_id' => array(
				'rule' => 'notEmpty'
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @type array
	 */
	public $admin = array(
		'iconClass' => 'icon-legal'
	);

	/**
	 * Validate a user isn't already moderating a forum.
	 *
	 * @param array $check
	 * @return bool
	 */
	public function checkUniqueMod($check) {
		return !$this->isModerator($this->data[$this->alias]['user_id'], $this->data[$this->alias]['forum_id']);
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
	 * Check if the user is a moderator.
	 *
	 * @param int $user_id
	 * @param int $forum_id
	 * @return bool
	 */
	public function isModerator($user_id, $forum_id) {
		return (bool) $this->find('count', array(
			'conditions' => array(
				'Moderator.user_id' => $user_id,
				'Moderator.forum_id' => $forum_id
			)
		));
	}

}
