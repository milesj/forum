<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumProfilesSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumProfiles';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_profiles = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'user_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'signature' => array(
			'type' => 'string',
			'length' => 255,
			'null' => false
		),
		'locale' => array(
			'type' => 'string',
			'length' => 3,
			'null' => false,
			'default' => 'eng'
		),
		'timezone' => array(
			'type' => 'string',
			'length' => 4,
			'null' => false,
			'default' => '-8'
		),
		'totalPosts' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'default' => 0
		),
		'totalTopics' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'default' => 0
		),
		'currentLogin' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null
		),
		'lastLogin' => array(
			'type' => 'datetime',
			'null' => true,
			'default' => null
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
			),
			'user_id' => array(
				'column' => 'user_id',
				'unique' => false
			)
		)
	);

}