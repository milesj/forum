<?php
/** 
 * Forum Plugin AppModel
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

App::import('Core', 'CakeSession');

class ForumAppModel extends AppModel {

	/**
	 * Togglable constants.
	 */
	const BOOL_YES = 1;
	const BOOL_NO = 0;

	/**
	 * Status constants.
	 */
	const STATUS_OPEN = 1;
	const STATUS_CLOSED = 0;

	/**
	 * Table prefix.
	 *
	 * @access public
	 * @var string
	 */
	public $tablePrefix = 'forum_';
	
	/**
	 * Database config.
	 *
	 * @access public
	 * @var string
	 */
	public $useDbConfig = 'default';

	/**
	 * Cache queries.
	 *
	 * @access public
	 * @var boolean
	 */
	public $cacheQueries = true;

	/**
	 * Behaviors.
	 *
	 * @access public
	 * @var array
	 */
	public $actsAs = array('Containable');

	/**
	 * No recursion.
	 *
	 * @access public
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Allow the model to interact with the sesion.
	 *
	 * @access public
	 * @param int $id
	 * @param string $table
	 * @param string $ds
	 * @return void
	 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->Session = new CakeSession();
		$this->settings = Configure::read('Forum.settings');

		if (Cache::config('sql') === false) {
			Cache::config('sql', array(
				'engine' 	=> 'File',
				'serialize' => true,
				'prefix'	=> '',
				'path' 		=> CACHE .'sql'. DS,
				'duration'	=> '+1 day'
			));
		}
	}

	/**
	 * Wrapper find() to cache sql queries.
	 *
	 * @access public
	 * @param array $conditions
	 * @param array $fields
	 * @param string $order
	 * @param string $recursive
	 * @return array
	 */
	public function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		if (!Configure::read('Cache.disable') && Configure::read('Cache.check') && !empty($fields['cache'])) {
			if (is_array($fields['cache'])) {
				$key = $fields['cache'][0];
				$expires = $fields['cache'][1];
			} else {
				$key = $fields['cache'];
				$expires = '+1 hour';
			}

			Cache::config('sql', array(
				'prefix' 	=> strtolower($this->name) .'__',
				'duration'	=> $expires
			));

			$results = Cache::read($key, 'sql');

			if (!is_array($results)) {
				$results = parent::find($conditions, $fields, $order, $recursive);
				
				Cache::write($key, $results, 'sql');
			}

			return $results;
		}

		// Not caching
		return parent::find($conditions, $fields, $order, $recursive);
	}

	/**
	 * Grab a row and defined fields/containables.
	 *
	 * @access public
	 * @param int $id
	 * @param array $fields
	 * @param array|boolean $contain
	 * @return array
	 */
	public function get($id, $fields = array(), $contain = false) {
		if (is_array($id)) {
			$column = $id[0];
			$value = $id[1];
		} else {
			$column = 'id';
			$value = $id;
		}

		if (empty($fields)) {
			$fields = $this->alias .'.*';
		} else {
			foreach ($fields as $row => $field) {
				$fields[$row] = $this->alias .'.'. $field;
			}
		}
		
		return $this->find('first', array(
			'conditions' => array($this->alias .'.'. $column => $value),
			'fields' => $fields,
			'contain' => $contain
		));
	}

	/**
	 * Get a count of all rows.
	 *
	 * @access public
	 * @return int
	 */
	public function getTotal() {
		return $this->find('count', array(
			'contain' => false,
			'recursive' => false,
			'cache' => array(__FUNCTION__, '+24 hours')
		));
	}

	/**
	 * Adds locale functions to errors.
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return string
	 */
	function invalidate($field, $value = true) {
		return parent::invalidate($field, __d('forum', $value, true));
	}
	
	/**
	 * Validates two inputs against each other.
	 *
	 * @access public
	 * @param array $data
	 * @param string $confirmField
	 * @return boolean
	 */
	public function isMatch($data, $confirmField) {
		$data = array_values($data);
		$var1 = $data[0];
		$var2 = isset($this->data[$this->name][$confirmField]) ? $this->data[$this->name][$confirmField] : '';

		return ($var1 === $var2);
	}

	/**
	 * Update a row with certain fields.
	 * 
	 * @access public
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function update($id, $data) {
		$this->id = $id;
		
		return $this->save($data, false, array_keys($data));
	}
	
}
  