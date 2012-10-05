<?php
/**
 * Forum - AccessLevel
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class AccessLevel extends ForumAppModel {

	/**
	 * Constants for DB levels.
	 */
	const GUEST = 0;
	const MEMBER = 1;
	const MOD = 4;
	const SUPER = 7;
	const ADMIN = 10;

	/**
	 * Behaviors.
	 *
	 * @access public
	 * @var array
	 */
	public $actsAs = array(
		'Utility.Filterable' => array(
			'title' => array('strip' => true)
		)
	);

	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'level' => 'notEmpty',
		'title' => 'notEmpty'
	);

	/**
	 * Enum.
	 *
	 * @access public
	 * @var array
	 */
	public $enum = array(
		'isSuper' => array(
			self::BOOL_NO => 'NO',
			self::BOOL_YES => 'YES'
		),
		'isAdmin' => array(
			self::BOOL_NO => 'NO',
			self::BOOL_YES => 'YES'
		)
	);

	/**
	 * Get a list of levels.
	 *
	 * @access public
	 * @param int $exclude
	 * @return array
	 */
	public function getHigherLevels($exclude = null) {
		$conditions = array('AccessLevel.level >' => self::MEMBER);

		if (is_numeric($exclude)) {
			$conditions['AccessLevel.id !='] = $exclude;
		}

		return $this->find('list', array(
			'fields' => array('AccessLevel.id', 'AccessLevel.title'),
			'conditions' => $conditions
		));
	}

	/**
	 * Get a list of levels.
	 *
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'order' => array('AccessLevel.level' => 'ASC'),
			'cache' => __METHOD__
		));
	}

}
