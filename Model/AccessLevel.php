<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
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
	 * @var array
	 */
	public $validate = array(
		'level' => 'notEmpty',
		'title' => 'notEmpty'
	);

	/**
	 * Enum.
	 *
	 * @var array
	 */
	public $enum = array(
		'isSuper' => array(
			self::NO => 'NO',
			self::YES => 'YES'
		),
		'isAdmin' => array(
			self::NO => 'NO',
			self::YES => 'YES'
		)
	);

	/**
	 * Get a list of levels.
	 *
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
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'order' => array('AccessLevel.level' => 'ASC'),
			'cache' => __METHOD__
		));
	}

}
