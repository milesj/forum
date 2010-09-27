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
	 * Table prefix.
	 *
	 * @access public
	 * @var string
	 */
	public $tablePrefix = '{:prefix}';

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
  