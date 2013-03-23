<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class PollVote extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Poll' => array(
			'className' => 'Forum.Poll'
		),
		'PollOption' => array(
			'className' => 'Forum.PollOption'
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
			'poll_id' => array(
				'rule' => 'notEmpty'
			),
			'poll_option_id' => array(
				'rule' => 'notEmpty'
			),
			'user_id' => array(
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
		$data = array(
			'poll_id' => $poll_id,
			'poll_option_id' => $option_id,
			'user_id' => $user_id
		);

		$this->create();

		return $this->save($data, false, array_keys($data));
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
