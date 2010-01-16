<?php
/**
 * Cupcake - Installer Controller
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/forum-plugin
 */

App::import('Core', 'File');
App::import('Model', 'ConnectionManager', false);
include_once CONFIGS . 'database.php';

class InstallController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array();

	/**
	 * Select which database to create the tables in.
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		if (!$this->Session->check('Install')) {
			$this->Session->write('Install', array());
		}

		// Get database configs
		$dbConfig = new DATABASE_CONFIG();
		$databases = array();
		foreach ($dbConfig as $db => $params) {
			$databases[$db] = $db;
		}

		// Set default prefix
		$this->data['prefix'] = 'forum_';

		$this->pageTitle = 'Step 1: Database Configuration';
		$this->set('databases', $databases);
	}

	/**
	 * Check to see if the database tables are taken, and that you can connect to the database.
	 *
	 * @access public
	 * @return void
	 */
	public function check_database() {
		if (isset($this->data['database'])) {
			$this->Session->write('Install.database', $this->data['database']);
		} else {
			$this->redirect(array('action' => 'index'));
		}

		$this->Session->write('Install.prefix', $this->data['prefix']);

		// Check database
		$db = ConnectionManager::getDataSource($this->data['database']);

		if ($db->isConnected()) {
			$tables = $this->__checkTables($db, $this->data['prefix']);
			$takenTables = array();
			$prefixTables = array();

			if (!empty($tables)) {
				foreach ($tables as $table => $value) {
					$prefixTables[] = $this->data['prefix'] . $table;

					if ($value == 1) {
						$takenTables[] = $table;
					}
				}
			}
			
			$this->set('isConnected', true);
			$this->set('tables', $prefixTables);
			$this->set('taken', $takenTables);
		} else {
			$this->set('isConnected', false);
		}

		$this->pageTitle = 'Step 2: Database Table Check';
	}

	/**
	 * Update the site settings.
	 *
	 * @access public
	 * @return void
	 */
	public function edit_settings() {
		if (isset($this->data['user_table'])) {
			$this->Session->write('Install.user_table', 'yes');
		}

		$this->pageTitle = 'Step 3: Edit Settings';
	}

	public function create_tables() {
		if (!empty($this->data)) {
			foreach ($this->data as $field => $value) {
				if ($field != '_Token') {
					$this->Session->write('Install.'. $field, $value);
				}
			}
		}

		debug($this->Session->read('Install'));
	}

	/**
	 * Check to see if the database tables have already been taken.
	 *
	 * @access private
	 * @param object $db
	 * @param string $prefix
	 * @return array
	 */
	private function __checkTables($db, $prefix = '') {
		$existent = $db->listSources();
		$tables = array_flip(array('access', 'access_levels', 'forums', 'forum_categories', 'moderators', 'polls', 'poll_options', 'poll_votes', 'posts', 'reported', 'topics', 'users'));

		foreach ($tables as $table => $value) {
			$tables[$table] = (in_array($prefix . $table, $existent) ? true : false);
		}

		return $tables;
	}

	/**
	 * Update the AppModel or User model and set the $tablePrefix property.
	 *
	 * @access private
	 * @param string $prefix
	 * @param string $file
	 * @return void
	 */
	private function __rewriteModel($prefix = '', $file = 'AppModel') {
		switch ($file) {
			case 'AppModel':	$path = dirname(__DIR__) . DS .'forum_app_model.php'; break;
			case 'UserModel':	$path = dirname(__DIR__) . DS .'models'. DS .'user.php'; break;
			default: return; break;
		}

		$contents = file_get_contents($path);
		$contents = String::insert($contents, array('prefix' => $prefix), array('before' => '{:', 'after' => '}'));

		$this->File = new File($path);
		$this->File->open('w', true);
		$this->File->write($contents);
		$this->File->close();
	}

	/**
	 * Update the raw SQL files and append the prefix to all tables.
	 *
	 * @access private
	 * @param string $prefix
	 * @return void
	 */
	private function __rewriteSql($prefix = '') {
		$path = dirname(__DIR__) . DS .'config'. DS .'schema'. DS;
		$schemas = array('schema.sql', 'users_alter.sql', 'users_create.sql');

		foreach ($schemas as $schema) {
			if (file_exists($path . $schema)) {
				$contents = file_get_contents($path . $schema);
				$contents = String::insert($contents, array('prefix' => $prefix), array('before' => '{:', 'after' => '}'));

				$this->File = new File($path .'prepared_'. $schema, true, 0777);
				$this->File->open('w', true);
				$this->File->write($contents);
				$this->File->close();
			}
		}
	}

	/**
	 * Before filter.
	 *
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		if ($this->action != 'index') {
			if (!$this->Session->check('Install')) {
				$this->redirect(array('action' => 'index'));
			}
		}

		if ($this->Session->check('Install.database')) {
			$this->DB =& ConnectionManager::getDataSource($this->Session->read('Install.database'));
		}

		debug($this->Session->read('Install'));

		// Set the installation layout
		$this->layout = 'install';

		// The usual
		$this->Auth->allow('*');
		$this->set('menuTab', '');
	}

}