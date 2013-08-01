<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('CakeSession', 'Model/Datasource');

use Decoda\Decoda;

class ForumAppModel extends AppModel {

	/**
	 * Toggleable constants.
	 */
	const YES = 1;
	const NO = 0;

	/**
	 * Status constants.
	 */
	const OPEN = 1;
	const CLOSED = 0;

	/**
	 * Table prefix.
	 *
	 * @type string
	 */
	public $tablePrefix = FORUM_PREFIX;

	/**
	 * Database config.
	 *
	 * @type string
	 */
	public $useDbConfig = FORUM_DATABASE;

	/**
	 * Cache queries.
	 *
	 * @type bool
	 */
	public $cacheQueries = true;

	/**
	 * No recursion.
	 *
	 * @type int
	 */
	public $recursive = -1;

	/**
	 * Behaviors.
	 *
	 * @type array
	 */
	public $actsAs = array(
		'Containable',
		'Utility.Enumerable',
		'Utility.Cacheable' => array(
			'cacheConfig' => 'forum'
		)
	);

	/**
	 * Global enum.
	 *
	 * @type array
	 */
	public $enum = array(
		'status' => array(
			self::OPEN => 'OPEN',
			self::CLOSED => 'CLOSED'
		)
	);

	/**
	 * Session instance.
	 *
	 * @type CakeSession
	 */
	public $Session;

	/**
	 * Allow the model to interact with the session.
	 *
	 * @param int $id
	 * @param string $table
	 * @param string $ds
	 */
	public function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->Session = new CakeSession();
	}

	/**
	 * Return all records.
	 *
	 * @return array
	 */
	public function getAll() {
		return $this->find('all', array(
			'contain' => false,
			'cache' => $this->alias . '::' . __FUNCTION__
		));
	}

	/**
	 * Return all records as a list.
	 *
	 * @return array
	 */
	public function getList() {
		return $this->find('list', array(
			'contain' => false,
			'cache' => $this->alias . '::' . __FUNCTION__
		));
	}

	/**
	 * Return a record based on ID.
	 *
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array($this->alias . '.id' => $id),
			'contain' => array_keys($this->belongsTo),
			'cache' => array($this->alias . '::' . __FUNCTION__, $id)
		));
	}

	/**
	 * Return a record based on slug.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function getBySlug($slug) {
		return $this->find('first', array(
			'conditions' => array($this->alias . '.slug' => $slug),
			'contain' => array_keys($this->belongsTo),
			'cache' => array($this->alias . '::' . __FUNCTION__, $slug)
		));
	}

	/**
	 * Get a count of all rows.
	 *
	 * @return int
	 */
	public function getTotal() {
		return $this->find('count', array(
			'contain' => false,
			'recursive' => false,
			'cache' => $this->alias . '::' . __FUNCTION__,
			'cacheExpires' => '+24 hours'
		));
	}

	/**
	 * Update a row with certain fields.
	 *
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, $data) {
		$this->id = $id;

		return $this->save($data, false, array_keys($data));
	}

	/**
	 * Validate the Decoda markup.
	 *
	 * @param string $model
	 * @param string $field
	 * @return bool
	 */
	public function validateDecoda($model, $field = 'content') {
		if (!isset($this->data[$model][$field])) {
			return true;
		}

		$decoda = new Decoda($this->data[$model][$field]);
		$decoda->defaults()->parse();
		$errors = $decoda->getErrors();

		if (!$errors) {
			return true;
		}

		$nesting = array();
		$closing = array();
		$scope = array();

		foreach ($errors as $error) {
			switch ($error['type']) {
				case Decoda::ERROR_NESTING:	$nesting[] = $error['tag']; break;
				case Decoda::ERROR_CLOSING:	$closing[] = $error['tag']; break;
				case Decoda::ERROR_SCOPE:	$scope[] = $error['child'] . ' -> ' . $error['parent']; break;
			}
		}

		if ($nesting) {
			return $this->invalid('content', 'The following tags have been nested in the wrong order: %s', implode(', ', $nesting));
		}

		if ($closing) {
			return $this->invalid('content', 'The following tags have no closing tag: %s', implode(', ', $closing));
		}

		if ($scope) {
			return $this->invalid('content', 'The following tags can not be placed within a specific tag: %s', implode(', ', $scope));
		}

		return true;
	}

}
