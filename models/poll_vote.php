<?php
/** 
 * poll_vote.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - PollVote Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class PollVote extends ForumAppModel {

	/**
	 * Belongs to
	 * @access public
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
			'className' => 'Forum.User'
		)
	);
	
	/**
	 * Add a voter for a poll
	 * @access public
	 * @param int $poll_id
	 * @param int $option_id
	 * @param int $user_id
	 * @return boolean
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
	 * Check to see if a person voted
	 * @access public
	 * @param int $user_id
	 * @param int $poll_id
	 * @return mixed
	 */
	public function hasVoted($user_id, $poll_id) {
		$vote = $this->find('first', array(
			'conditions' => array('PollVote.poll_id' => $poll_id, 'PollVote.user_id' => $user_id),
			'contain' => false
		));
		
		return (empty($vote)) ? 'no' : $vote['PollVote']['poll_option_id'];
	}
	
}
