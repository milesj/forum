<?php
/**
 * Forum: A CakePHP Plugin
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

class ForumForumCategoriesSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumForumCategories';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_forum_categories = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'key' => 'primary'
		),
		'forum_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'parent_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => true,
			'default' => null
		),
		'access_level_id' => array(
			'type' => 'integer',
			'length' => 5,
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
		'description' => array(
			'type' => 'string',
			'length' => 255,
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
		'topic_count' => array(
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
		'accessRead' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 0
		),
		'accessPost' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 1
		),
		'accessReply' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 1
		),
		'accessPoll' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 1
		),
		'settingPostCount' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 1
		),
		'settingAutoLock' => array(
			'type' => 'integer',
			'length' => 5,
			'null' => false,
			'default' => 1
		),
		'lastTopic_id' => array(
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
			'lastUser_id' => array(
				'column' => 'lastUser_id',
				'unique' => false
			),
			'lastPost_id' => array(
				'column' => 'lastPost_id',
				'unique' => false
			),
			'lastTopic_id' => array(
				'column' => 'lastTopic_id',
				'unique' => false
			),
			'forum_id' => array(
				'column' => 'forum_id',
				'unique' => false
			),
			'parent_id' => array(
				'column' => 'parent_id',
				'unique' => false
			),
			'access_level_id' => array(
				'column' => 'access_level_id',
				'unique' => false
			)
		)
	);

}