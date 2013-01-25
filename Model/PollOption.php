<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class PollOption extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Poll' => array(
			'className' => 'Forum.Poll'
		)
	);

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Utility.Filterable' => array(
			'option' => array(
				'html' => true,
				'strip' => true
			)
		)
	);

	/**
	 * Add a vote for a poll.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function addVote($id) {
		return $this->query('UPDATE `' . $this->tablePrefix . 'poll_options` AS `PollOption` SET `PollOption`.`vote_count` = `PollOption`.`vote_count` + 1 WHERE `PollOption`.`id` = ' . (int) $id);
	}

}
