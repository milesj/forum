<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumReportedSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumReported';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_reported = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'item_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'user_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'itemType' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false
		),
		'comment' => array(
			'type' => 'string',
			'null' => false,
			'length' => 255
		),
		'created' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null
		),
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			),
			'item_id' => array(
				'column' => 'item_id',
				'unique' => false
			),
			'user_id' => array(
				'column' => 'user_id',
				'unique' => false
			)
		)
	);

}