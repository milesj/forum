<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumTopicsSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumTopics';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_topics = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'forum_category_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'user_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'title' => array(
			'type' => 'string',
			'length' => 100,
			'null' => false
		),
		'slug' => array(
			'type' => 'string',
			'length' => 110,
			'null' => false
		),
		'status' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'type' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'view_count' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'default' => 0
		),
		'post_count' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'default' => 0
		),
		'firstPost_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => true,
			'default' => null
		),
		'lastPost_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => true,
			'default' => null
		),
		'lastUser_id' => array(
			'type' => 'integer',
			'length' => 10,
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
			'forum_category_id' => array(
				'column' => 'forum_category_id',
				'unique' => false
			),
			'user_id' => array(
				'column' => 'user_id',
				'unique' => false
			),
			'lastUser_id' => array(
				'column' => 'lastUser_id',
				'unique' => false
			),
			'lastPost_id' => array(
				'column' => 'lastPost_id',
				'unique' => false
			),
			'firstPost_id' => array(
				'column' => 'firstPost_id',
				'unique' => false
			)
		)
	);

}