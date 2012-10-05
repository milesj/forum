<?php
/**
 * Forum - Poll
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class Poll extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Topic' => array(
			'className' => 'Forum.Topic'
		)
	);

	/**
	 * Has many.
	 *
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'PollOption' => array(
			'className' => 'Forum.PollOption',
			'exclusive' => true,
			'dependent' => true,
			'order' => array('PollOption.id' => 'ASC'),
		),
		'PollVote' => array(
			'className' => 'Forum.PollVote',
			'exclusive' => true,
			'dependent' => true
		)
	);

	/**
	 * Add a poll attached to a topic.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function addPoll($data) {
		$poll = array(
			'topic_id' => $data['topic_id'],
			'expires' => !empty($data['expires']) ? date('Y-m-d H:i:s', strtotime('+' . $data['expires'] . ' days')) : null
		);

		if ($this->save($poll, false, array('topic_id', 'expires'))) {
			$poll_id = $this->id;
			$options = explode("\n", $data['options']);
			$results = array(
				'poll_id' => $poll_id,
				'vote_count' => 0
			);

			foreach ($options as $opt) {
				if ($opt) {
					$results['option'] = trim($opt);

					$this->PollOption->create();
					$this->PollOption->save($results, false, array_keys($results));
				}
			}

			return $poll_id;
		}

		return false;
	}

	/**
	 * Process the totals and percentages.
	 *
	 * @access public
	 * @param array $poll
	 * @return array
	 */
	public function process($poll) {
		$user_id = $this->Session->read('Auth.User.id');

		if ($poll) {
			$totalVotes = 0;

			foreach ($poll['PollOption'] as $option) {
				$totalVotes = $totalVotes + $option['vote_count'];
			}

			foreach ($poll['PollOption'] as &$option) {
				$option['percentage'] = ($totalVotes > 0) ? round(($option['vote_count'] / $totalVotes) * 100) : 0;
			}

			$poll['hasVoted'] = $this->PollVote->hasVoted($user_id, $poll['id']);
			$poll['totalVotes'] = $totalVotes;
		}

		return $poll;
	}

	/**
	 * Vote in a poll.
	 *
	 * @access public
	 * @param int $poll_id
	 * @param int $option_id
	 * @param int $user_id
	 * @return boolean
	 */
	public function vote($poll_id, $option_id, $user_id) {
		$poll = $this->find('first', array(
			'conditions' => array('Poll.id' => $poll_id),
			'contain' => false
		));

		if ($poll) {
			if (!empty($poll['Poll']['expires']) && $poll['Poll']['expires'] <= date('Y-m-d H:i:s')) {
				return false;
			}

			if (!$this->PollVote->hasVoted($user_id, $poll_id)) {
				$this->PollOption->addVote($option_id);
				$this->PollVote->addVoter($poll_id, $option_id, $user_id);
			}
		}

		return true;
	}

}
