<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumAccessLevelsSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumAccessLevels';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_access_levels = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'title' => array(
			'type' => 'string',
			'length' => 30,
			'null' => false
		),
		'level' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'isAdmin' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'isSuper' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'indexes' => array(
			'PRIMARY' => array(
				'column' => 'id',
				'unique' => true
			)
		)
	);

}