<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property User $User
 * @property Poll $Poll
 * @property PollOption $PollOption
 */
class PollVote extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @type array
	 */
	public $belongsTo = array(
		'Poll' => array(
			'className' => 'Forum.Poll'
		),
		'PollOption' => array(
			'className' => 'Forum.PollOption',
			'counterCache' => true
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
			'poll_id' => array(
				'rule' => 'notEmpty'
			),
			'poll_option_id' => array(
				'rule' => 'notEmpty'
			),
			'user_id' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty'
				),
				'checkHasVoted' => array(
					'rule' => 'checkHasVoted',
					'message' => 'This user has already voted'
				)
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @type array
	 */
	public $admin = array(
		'iconClass' => 'icon-list-ol'
	);

	/**
	 * Add a voter for a poll.
	 *
	 * @param int $poll_id
	 * @param int $option_id
	 * @param int $user_id
	 * @return bool
	 */
	public function addVoter($poll_id, $option_id, $user_id) {
		if ($this->hasVoted($user_id, $poll_id)) {
			return true;
		}

		$data = array(
			'poll_id' => $poll_id,
			'poll_option_id' => $option_id,
			'user_id' => $user_id
		);

		$this->create();

		return $this->save($data, false, array_keys($data));
	}

	/**
	 * Validate a user hasn't voted.
	 *
	 * @return bool
	 */
	public function checkHasVoted() {
		return !$this->hasVoted($this->data[$this->alias]['user_id'], $this->data[$this->alias]['poll_id']);
	}

	/**
	 * Check to see if a person voted.
	 *
	 * @param int $user_id
	 * @param int $poll_id
	 * @return mixed
	 */
	public function hasVoted($user_id, $poll_id) {
		$vote = $this->find('first', array(
			'conditions' => array(
				'PollVote.poll_id' => $poll_id,
				'PollVote.user_id' => $user_id
			),
			'contain' => false
		));

		if ($vote) {
			return $vote['PollVote']['poll_option_id'];
		}

		return false;
	}

}
