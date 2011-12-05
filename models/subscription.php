<?php
/** 
 * Forum - Subscription
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */
 
class Subscription extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'User',
		'Forum' => array(
			'className'		=> 'Forum.Forum',
			'foreignKey'	=> 'forum_id'
		),
		'Topic' => array(
			'className' 	=> 'Forum.Topic',
			'foreignKey'	=> 'topic_id'
		)
	);
	
}