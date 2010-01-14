<?php
/** 
 * Cupcake - PollOption Model
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/forum-plugin
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
		return $this->query("UPDATE `". $this->tablePrefix ."poll_options` AS `PollOption` SET `PollOption`.`vote_count` = `PollOption`.`vote_count` + 1 WHERE `PollOption`.`id` = $id");
	}
	
}
