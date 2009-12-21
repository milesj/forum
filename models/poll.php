<?php
/** 
 * poll.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Poll Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class Poll extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Topic' => array(
			'className' => 'Forum.Topic'
		)
	);

	/**
	 * Has many
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'PollOption' => array(
			'className'	=> 'Forum.PollOption',
			'exclusive' => true,
			'dependent' => true,
			'order' 	=> 'PollOption.id ASC',
		),
		'PollVote' => array(
			'className' => 'Forum.PollVote',
			'exclusive' => true,
			'dependent' => true
		)
	);
	
	/**
	 * Add a poll attached to a topic
	 * @access public
	 * @param int $topic_id
	 * @param array $data
	 * @return boolean
	 */
	public function addPoll($topic_id, $data) {
		$poll = array(
			'topic_id' => $topic_id,
			'expires' => (!empty($data['expires'])) ? date('Y-m-d H:i:s', strtotime('+'. $data['expires'] .' days')) : NULL
		);
		
		if ($this->save($poll, false, array('topic_id', 'expires'))) {
			$poll_id = $this->id;
			$options = explode("\n", strip_tags($data['options']));
			$results = array(
				'poll_id' => $poll_id,
				'vote_count' => 0
			);
			
			foreach ($options as $id => $opt) {
				if (!empty($opt)) {
					$results['option'] = htmlentities($opt, ENT_NOQUOTES, 'UTF-8');
					$this->PollOption->create();
					$this->PollOption->save($results, false, array_keys($results));
				}
			}
			
			return $poll_id;
		}
		
		return false;
	}
	
	/**
	 * Process the totals and percentages
	 * @access public
	 * @param array $poll
	 * @param int $user_id
	 * @return array
	 */
	public function process($poll, $user_id) {
		if (!empty($poll)) {
		
			// Total votes
			$totalVotes = 0;
			foreach ($poll['PollOption'] as $option) {
				$totalVotes = $totalVotes + $option['vote_count'];
			}
			$poll['totalVotes'] = $totalVotes;
			
			// Percentage
			foreach ($poll['PollOption'] as &$option) {
				$percent = ($totalVotes > 0) ? round(($option['vote_count'] / $totalVotes) * 100) : 0;
				$option['percentage'] = $percent;
			}
			
			// Has voted
			$poll['hasVoted'] = $this->PollVote->hasVoted($user_id, $poll['id']);
		}
		
		return $poll;
	}
	
	/**
	 * Vote in a poll
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
		
		if (!empty($poll)) {
			if (!empty($poll['Poll']['expires']) && $poll['Poll']['expires'] <= date('Y-m-d H:i:s')) {
				return false;
			}
			
			// Has user voted?
			$voted = $this->PollVote->find('count', array(
				'conditions' => array(
					'PollVote.poll_id' => $poll_id, 
					'PollVote.user_id' => $user_id
				),
				'contain' => false
			));
			
			if ($voted <= 0) {
				$this->PollOption->addVote($option_id);
				$this->PollVote->addVoter($poll_id, $option_id, $user_id);
			}
		}
		
		return true;
	}
	
}
