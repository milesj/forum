<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class PollOption extends ForumAppModel {

	/**
	 * Display field.
	 *
	 * @var string
	 */
	public $displayField = 'option';

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Poll' => array(
			'className' => 'Forum.Poll'
		)
	);

	/**
	 * Has many.
	 *
	 * @var array
	 */
	public $hasMany = array(
		'PollVote' => array(
			'className' => 'Forum.PollVote',
			'limit' => 100
		)
	);

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Utility.Filterable' => array(
			'option' => array(
				'html' => true,
				'strip' => true
			)
		)
	);

	/**
	 * Validation.
	 *
	 * @var array
	 */
	public $validations = array(
		'default' => array(
			'poll_id' => array(
				'rule' => 'notEmpty'
			),
			'option' => array(
				'rule' => 'notEmpty'
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-list'
	);

}
