<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumPollsSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumPolls';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_polls = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'topic_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'created' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null
		),
		'modified' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null
		),
		'expires' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null
		),
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			),
			'topic_id' => array(
				'column' => 'topic_id',
				'unique' => false
			)
		)
	);

}