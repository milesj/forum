<?php
/** 
 * Forum - Report
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */
 
class Report extends ForumAppModel {

	/**
	 * Report types.
	 */
	const TOPIC = 1;
	const POST = 2;
	const USER = 3;

	/**
	 * DB Table.
	 *
	 * @access public
	 * @var string
	 */
	public $useTable = 'reported';
	
	/**
	 * Belongs to.
	 *
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Reporter' => array(
			'className'		=> 'User',
			'foreignKey'	=> 'user_id'
		),
		'Topic' => array(
			'className' 	=> 'Forum.Topic',
			'foreignKey' 	=> 'item_id'
		),
		'Post' => array(
			'className' 	=> 'Forum.Post',
			'foreignKey' 	=> 'item_id'
		),
		'User' => array(
			'foreignKey' 	=> 'item_id'
		)
	);
	
	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'comment' => 'notEmpty'
	);
	
	/**
	 * Get the latest reports.
	 * 
	 * @access public
	 * @param $limit
	 * @return array
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'limit' => $limit,
			'order' => array('Report.created' => 'ASC'),
			'contain' => array('Reporter', 'Topic', 'Post', 'User')
		));
	}
	
}
