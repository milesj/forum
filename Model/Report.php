<?php
/**
 * Forum - Report
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

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
			'className' => FORUM_USER,
			'foreignKey' => 'user_id'
		),
		'Topic' => array(
			'className' => 'Forum.Topic',
			'foreignKey' => 'item_id'
		),
		'Post' => array(
			'className' => 'Forum.Post',
			'foreignKey' => 'item_id'
		),
		'User' => array(
			'className' => FORUM_USER,
			'foreignKey' => 'item_id'
		)
	);

	/**
	 * Behaviors.
	 *
	 * @access public
	 * @var array
	 */
	public $actsAs = array(
		'Utility.Filterable' => array(
			'comment' => array('strip' => true)
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
	 * Enum.
	 *
	 * @access public
	 * @var array
	 */
	public $enum = array(
		'itemType' => array(
			self::TOPIC => 'TOPIC',
			self::POST => 'POST',
			self::USER => 'USER'
		)
	);

	/**
	 * Get the latest reports.
	 *
	 * @access public
	 * @param int $limit
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
