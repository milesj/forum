<?php
/** 
 * report.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Report Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum 
 */
 
class Report extends ForumAppModel {

	/**
	 * DB Table
	 * @access public
	 * @var string
	 */
	public $useTable = 'reported';
	
	/**
	 * Belongs to
	 * @access public
	 * @var array
	 */
	public $belongsTo = array(
		'Reporter' => array(
			'className' 	=> 'Forum.User',
			'foreignKey'	=> 'user_id'
		),
		'Topic' => array(
			'className' 	=> 'Forum.Topic',
			'foreignKey' 	=> 'item_id',
			'conditions' 	=> array('Report.itemType' => 'topic')
		),
		'Post' => array(
			'className' 	=> 'Forum.Post',
			'foreignKey' 	=> 'item_id',
			'conditions' 	=> array('Report.itemType' => 'post')
		),
		'User' => array(
			'className' 	=> 'Forum.User',
			'foreignKey' 	=> 'item_id',
			'conditions' 	=> array('Report.itemType' => 'user')
		)
	);
	
	/**
	 * Validation
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'comment' => 'notEmpty'
	);
	
	/**
	 * Get the latest reports
	 * @access public
	 * @param $limit
	 * @return array
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'limit' => $limit,
			'order' => 'Report.created ASC',
			'contain' => array('Reporter', 'Topic.id', 'Topic.title', 'Post.id', 'Post.content', 'User.id', 'User.username')
		));
	}
	
}
