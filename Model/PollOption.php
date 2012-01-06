<?php
/** 
 * Forum - PollOption
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */
 
class PollOption extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Poll' => array(
			'className' => 'Forum.Poll'
		)
	);
	
	/**
	 * Add a vote for a poll.
	 *
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function addVote($id) {
		return $this->query('UPDATE `'. $this->tablePrefix .'poll_options` AS `PollOption` SET `PollOption`.`vote_count` = `PollOption`.`vote_count` + 1 WHERE `PollOption`.`id` = '. (int) $id);
	}
	
}
