<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumPollVotesSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumPollVotes';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_poll_votes = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'poll_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'poll_option_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'user_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			),
			'poll_id' => array(
				'column' => 'poll_id',
				'unique' => false
			),
			'poll_option_id' => array(
				'column' => 'poll_option_id',
				'unique' => false
			),
			'user_id' => array(
				'column' => 'user_id',
				'unique' => false
			)
		)
	);

}