<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property Topic $Topic
 * @property PollOption $PollOption
 * @property PollVote $PollVote
 */
class Poll extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @type array
	 */
	public $belongsTo = array(
		'Topic' => array(
			'className' => 'Forum.Topic'
		)
	);

	/**
	 * Has many.
	 *
	 * @type array
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
			'limit' => 100,
			'exclusive' => true,
			'dependent' => true
		)
	);

	/**
	 * Validation.
	 *
	 * @type array
	 */
	public $validations = array(
		'default' => array(
			'topic_id' => array(
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
		'iconClass' => 'icon-bar-chart'
	);

	/**
	 * Add a poll attached to a topic.
	 *
	 * @param array $data
	 * @return bool
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
				'poll_vote_count' => 0
			);

			foreach ($options as $opt) {
				if ($opt = trim($opt)) {
					$results['option'] = $opt;

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
	 * @param array $poll
	 * @return array
	 */
	public function process($poll) {
		$user_id = $this->Session->read(AuthComponent::$sessionKey . '.id');

		if ($poll) {
			$totalVotes = 0;

			foreach ($poll['PollOption'] as $option) {
				$totalVotes = $totalVotes + $option['poll_vote_count'];
			}

			foreach ($poll['PollOption'] as &$option) {
				$option['percentage'] = ($totalVotes > 0) ? round(($option['poll_vote_count'] / $totalVotes) * 100) : 0;
			}

			$poll['hasVoted'] = $this->PollVote->hasVoted($user_id, $poll['id']);
			$poll['totalVotes'] = $totalVotes;
		}

		return $poll;
	}

	/**
	 * Vote in a poll.
	 *
	 * @param int $poll_id
	 * @param int $option_id
	 * @param int $user_id
	 * @return bool
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

			$this->PollVote->addVoter($poll_id, $option_id, $user_id);
		}

		return true;
	}

}
