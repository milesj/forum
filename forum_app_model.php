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
		$this->config = Configure::read('Forum');
		$this->settings = Configure::read('Forum.settings');

		if (Cache::config('forum') === false) {
			Cache::config('forum', array(
				'engine' 	=> 'File',
				'serialize' => true,
				'prefix'	=> '',
				'path' 		=> CACHE .'forum'. DS,
				'duration'	=> '+1 day'
			));
		}
	}
	
	/**
	 * Get the users highest access level.
	 * 
	 * @access public
	 * @return int
	 */
	public function access() {
		return $this->Session->read('Forum.access');
	}
	
	/**
	 * Return an array of access levels or IDs.
	 * 
	 * @access public
	 * @param string $field
	 * @return array
	 */
	public function accessLevels($field = 'id') {
		$levels = array(0) + (array) $this->Session->read('Forum.accessLevels');
		
		if ($field == 'id') {
			$levels = array_keys($levels);
		}
		
		return $levels;
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
			
			Cache::config('forum', array('duration' => $expires));

			$key = $this->name .'.'. $key;
			$results = Cache::read($key, 'forum');

			if (!is_array($results)) {
				$results = parent::find($conditions, $fields, $order, $recursive);
				
				Cache::write($key, $results, 'forum');
			}

			return $results;
		}

		// Not caching
		return parent::find($conditions, $fields, $order, $recursive);
	}
	
	/**
	 * Return data based on ID.
	 * 
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		return $this->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => false
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
	 * @access public
	 * @param string $field
	 * @param mixed $value
	 * @param mixed $param
	 * @return string
	 */
	public function invalidate($field, $value = true, $param = '') {
		return parent::invalidate($field, sprintf(__d('forum', $value, true), $param));
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
	
	/**
	 * Validate the Decoda markup.
	 * 
	 * @access public
	 * @param string $model
	 * @return boolean
	 */
	public function validateDecoda($model) {
		$censored = array_map('trim', explode(',', $this->settings['censored_words']));
		$locale = $this->config['decodaLocales'][Configure::read('Config.language')];

		$decoda = new Decoda($this->data[$model]['content']);
		$decoda->defaults()->setXhtml()->setLocale($locale);
		$decoda->getHook('Censor')->blacklist($censored);

		$parsed = $decoda->parse();
		$errors = $decoda->getErrors();

		if (empty($errors)) {
			$this->data[$model]['contentHtml'] = $parsed;

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

		if (!empty($nesting)) {
			return $this->invalidate('content', 'The following tags have been nested in the wrong order: %s', implode(', ', $nesting));
		}

		if (!empty($closing)) {
			return $this->invalidate('content', 'The following tags have no closing tag: %s', implode(', ', $closing));
		}

		if (!empty($scope)) {
			return $this->invalidate('content', 'The following tags can not be placed within a specific tag: %s', implode(', ', $scope));
		}
		
		return true;
	}
	
}
  