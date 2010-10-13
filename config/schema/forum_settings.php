<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumSettingsSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumSettings';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_settings = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'key' => array(
			'type' => 'string',
			'length' => 50,
			'null' => false
		),
		'value' => array(
			'type' => 'string',
			'length' => 100,
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
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			)
		)
	);

}