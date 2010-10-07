<?php

class ForumAccessSchema extends CakeSchema {

	/**
	 * Schema name.
	 *
	 * @access public
	 * @var string
	 */
	public $name = 'ForumAccess';

	/**
	 * Table schema.
	 *
	 * @access public
	 * @var array
	 */
	public $forum_access = array(
		'id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false,
			'default' => null,
			'key' => 'primary'
		),
		'access_level_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
		),
		'user_id' => array(
			'type' => 'integer',
			'length' => 10,
			'null' => false
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
			'user_id' => array(
				'column' => 'user_id',
				'unique' => false
			),
			'access_level_id' => array(
				'column' => 'access_level_id',
				'unique' => false
			)
		)
	);
	
}