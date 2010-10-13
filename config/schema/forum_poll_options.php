<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumPollOptionsSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumPollOptions';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_poll_options = array(
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
		'vote_count' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'default' => 0
		),
		'option' => array(
			'type' => 'string',
			'null' => false,
			'length' => 100
		),
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			),
			'poll_id' => array(
				'column' => 'poll_id',
				'unique' => false
			)
		)
	);

}