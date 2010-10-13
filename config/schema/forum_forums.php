<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumForumsSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumForums';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_forums = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'access_level_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => true,
			'default' => null
		),
		'title' => array(
			'type' => 'string',
			'length' => 50,
			'null' => false
		),
		'slug' => array(
			'type' => 'string',
			'length' => 60,
			'null' => false
		),
		'status' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'orderNo' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'accessView' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			),
			'access_level_id' => array(
				'column' => 'access_level_id',
				'unique' => false
			)
		)
	);

}