<?php
/**
 * ForumAppModel
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('CakeSession', 'Model/Datasource');

class ForumAppModel extends AppModel {

	/**
	 * Toggleable constants.
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
	public $actsAs = array(
		'Containable',
		'Utility.Cacheable' => array(
			'cacheConfig' => 'forum',
			'appendKey' => false,
			'expires' => '+1 minute',
			'events' => array(
				'onCreate' => false
			)
		)
	);

	/**
	 * No recursion.
	 *
	 * @access public
	 * @var int
	 */
	public $recursive = -1;

	/**
	 * Global enum.
	 *
	 * @access public
	 * @var array
	 */
	public $enum = array(
		'status' => array(
			self::STATUS_CLOSED => 'CLOSED',
			self::STATUS_OPEN => 'OPEN'
		)
	);

	/**
	 * Plugin configuration.
	 *
	 * @access public
	 * @var array
	 */
	public $config = array();

	/**
	 * Database forum settings.
	 *
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * Session instance.
	 *
	 * @access public
	 * @var CakeSession
	 */
	public $Session;

	/**
	 * Allow the model to interact with the session.
	 *
	 * @access public
	 * @param int $id
	 * @param string $table
	 * @param string $ds
	 */
	public function __construct($id = null, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->Session = new CakeSession();
		$this->config = Configure::read('Forum');
		$this->settings = Configure::read('Forum.settings');
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

		if ($field === 'id') {
			$levels = array_keys($levels);
		}

		return $levels;
	}

	/**
	 * Return all records.
	 *
	 * @access public
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
	 * @access public
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
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => false,
			'cache' => array($this->alias . '::' . __FUNCTION__, $id)
		));
	}

	/**
	 * Return a record based on slug.
	 *
	 * @access public
	 * @param string $slug
	 * @return array
	 */
	public function getBySlug($slug) {
		return $this->find('first', array(
			'conditions' => array('slug' => $slug),
			'contain' => false,
			'cache' => array($this->alias . '::' . __FUNCTION__, $slug)
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
			'cache' => $this->alias . '::' . __FUNCTION__,
			'cacheExpires' => '+24 hours'
		));
	}

	/**
	 * Adds locale functions to errors.
	 *
	 * @access public
	 * @param string $field
	 * @param mixed $value
	 * @param mixed $param
	 * @return boolean
	 */
	public function invalidate($field, $value = true, $param = '') {
		parent::invalidate($field, sprintf(__d('forum', $value), $param));

		return false;
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
		$decoda->setXhtml(true)->setLocale($locale);

		// Filters
		$decoda->addFilter(new BlockFilter());
		$decoda->addFilter(new CodeFilter());
		$decoda->addFilter(new DefaultFilter());
		$decoda->addFilter(new EmailFilter());
		$decoda->addFilter(new ImageFilter());
		$decoda->addFilter(new ListFilter());
		$decoda->addFilter(new QuoteFilter());
		$decoda->addFilter(new TextFilter());
		$decoda->addFilter(new UrlFilter());

		// Hooks
		$censorHook = new CensorHook();
		$censorHook->blacklist($censored);

		$decoda->addHook($censorHook);
		$decoda->addHook(new ClickableHook());

		// Parse
		$parsed = $decoda->parse();
		$errors = $decoda->getErrors();

		if (!$errors) {
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

		if ($nesting) {
			return $this->invalidate('content', 'The following tags have been nested in the wrong order: %s', implode(', ', $nesting));
		}

		if ($closing) {
			return $this->invalidate('content', 'The following tags have no closing tag: %s', implode(', ', $closing));
		}

		if ($scope) {
			return $this->invalidate('content', 'The following tags can not be placed within a specific tag: %s', implode(', ', $scope));
		}

		return true;
	}

}
