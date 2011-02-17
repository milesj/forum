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

// Installation Constants
define('FORUM_PLUGIN', dirname(dirname(__FILE__)) . DS);
define('FORUM_CONFIG', FORUM_PLUGIN .'config'. DS);
define('FORUM_SCHEMA', FORUM_CONFIG .'schema'. DS);

class InstallController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array();

	/**
	 * Default page with directions.
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$this->Session->delete('Install');
		
		$this->pageTitle = 'Forum Installation';
	}

	/**
	 * Select which database to create the tables in.
	 *
	 * @access public
	 * @return void
	 */
	public function check_database() {
		$this->__checkInstallation();
		$processed = false;

		// Check database
		if ($this->RequestHandler->isPost()) {
			$this->Session->write('Install.database', $this->data['database']);
			$this->Session->write('Install.prefix', $this->data['prefix']);

			$this->DB = ConnectionManager::getDataSource($this->data['database']);
			
			if ($this->DB->isConnected()) {
				$tables = $this->__checkTables($this->data['prefix']);
				$conflicts = array_keys(array_filter($tables));

				$this->set('isConnected', true);
				$this->set('conflicts', $conflicts);
			} else {
				$this->set('isConnected', false);
			}
			
			$processed = true;
		} else {
			$this->data['prefix'] = 'forum_';
		}
		
		$this->pageTitle = 'Step 1: Database Configuration';
		$this->set('databases', $this->__getDatabases());
		$this->set('processed', $processed);
	}

	/**
	 * Create the actual tables.
	 *
	 * @access public
	 * @return void
	 */
	public function create_tables() {
		$this->__checkInstallation();
		
		if (!$this->Session->check('Install.database')) {
			$this->redirect(array('action' => 'index'));
		}

		// Rewrite SQL
		$this->__rewriteSql($this->Session->read('Install.prefix'));

		// Execute!
		$tables = $this->__getTables($this->Session->read('Install.prefix'));
		$total	= count($tables);
		$schema = explode(";", file_get_contents(FORUM_SCHEMA .'parsed_schema.sql'));
		$database = $this->Session->read('Install.database');
		$executed = 0;

		foreach ($schema as $sql) {
			$sql = trim($sql);

			if (!empty($sql)) {
				if ($this->DB->execute($sql)) {
					$command = trim(substr($sql, 0, 6));

					if (($command == 'CREATE') || ($command == 'ALTER')) {
						$executed++;
					}
				}
			}
		}

		// Final check, rollback tables if failure
		if ($executed != $total) {
			$this->__rollback();
			$this->Session->delete('Install');
		} else {
			$this->Session->write('Install.created', true);
		}

		$this->pageTitle = 'Step 2: Create Tables';
		$this->set('database', $database);
		$this->set('executed', $executed);
		$this->set('tables', $tables);
		$this->set('total', $total);
	}

	/**
	 * Check the users table and what to do.
	 *
	 * @access public
	 * @return void
	 */
	public function setup_users() {
		$this->__checkInstallation();
		$processed = false;
		$executed = false;

		if (!$this->Session->check('Install.created')) {
			$this->redirect(array('action' => 'index'));
		}

		// Create tables
		if ($this->RequestHandler->isPost()) {
			$this->Session->write('Install.user_table', ($this->data['action'] == 'sqlAlter' ? 1 : 0));

			$schema	= $this->data[$this->data['action']];
			$processed = true;
			
			if ($this->DB->execute($schema)) {
				$executed = true;
			} else {
				$this->__rollback('users');
			}
		} else {
			$this->data['sqlCreate'] = trim(file_get_contents(FORUM_SCHEMA .'parsed_users_create.sql'));
			$this->data['sqlAlter'] = trim(file_get_contents(FORUM_SCHEMA .'parsed_users_alter.sql'));
		}

		$this->pageTitle = 'Step 3: Setup Users Table';
		$this->set('processed', $processed);
		$this->set('executed', $executed);
		$this->set('database', $this->Session->read('Install.database'));
	}

	/**
	 * Finish the installation process.
	 *
	 * @access public
	 * @return void
	 */
	public function finished() {
		$this->__checkInstallation();
		
		if (!$this->Session->check('Install.created')) {
			$this->redirect(array('action' => 'index'));
		}

		// Update models
		$prefix = $this->Session->read('Install.prefix');
		$userPrefix = (($this->Session->read('Install.user_table') == 1) ? '' : $prefix);

		$this->__rewriteModel($prefix, 'AppModel');
		$this->__rewriteModel($userPrefix, 'UserModel');

		// Save the settings
		$this->__saveInstall();
		$this->__saveRouting();

		$this->pageTitle = 'Step 4: Finished';
	}

	/**
	 * Create an admin user.
	 *
	 * @access public
	 * @return void
	 */
	public function create_admin() {
		if (!ForumConfig::isInstalled()) {
			$this->Session->delete('Install');
			$this->redirect(array('action' => 'check_database'));
		}

		$this->loadModel('Forum.User');
		$this->loadModel('Forum.Access');
		
		$total = $this->User->find('count');
		$granted = false;

		if ($this->RequestHandler->isPost()) {
			$access = array('access_level_id' => 4);

			// Create user
			if ($total == 0) {
				$this->User->create();
				$this->User->set($this->data);
				$this->User->action = 'signup';

				if ($this->User->validates()) {
					$this->data['User']['username'] = strip_tags($this->data['User']['username']);
					$this->data['User']['password'] = Security::hash($this->data['User']['newPassword'], null, true);

					if ($this->User->save($this->data, false, array('username', 'email', 'password'))) {
						$granted = true;
						$access['user_id'] = $this->User->id;
					}
				}

			// Based on ID
			} else {
				if (!empty($this->data['User']['user_id'])) {
					$exists = $this->User->findById($this->data['User']['user_id']);

					if (!empty($exists)) {
						$granted = true;
						$access['user_id'] = $this->data['User']['user_id'];
					} else {
						$this->User->invalidate('user_id', 'No user exists with that ID');
					}
				} else {
					$this->User->invalidate('user_id', 'Please enter a user ID');
				}
			}

			// Give access
			if ($granted) {
				$this->Access->save($access, false);
			}
		}

		$this->pageTitle = 'Create Admin';
		$this->set('total', $total);
		$this->set('granted', $granted);
	}

	/**
	 * Patch your installation!
	 *
	 * @access public
	 * @return void
	 */
	public function patch() {
		if ($this->RequestHandler->isPost()) {
			$data = $this->data;
			$data['created'] = true;
			unset($data['_Token']);

			$this->Session->write('Install', $data);
			$this->set('patchMsg', 'Your plugin has successfully been patched!');

			$prefix = $data['prefix'];
			$userPrefix = (($data['user_table'] == 1) ? '' : $prefix);

			$this->__rewriteModel($prefix, 'AppModel');
			$this->__rewriteModel($userPrefix, 'UserModel');
			$this->__saveInstall();
		}

		$installed = ForumConfig::isInstalled();

		if (!$installed) {
			$this->data['prefix'] = 'forum_';
			$this->set('databases', $this->__getDatabases());
		}
		
		$this->pageTitle = 'Patch Installation';
		$this->set('installed', $installed);
	}

	/**
	 * Check if the plugin was installed.
	 * 
	 * @access private
	 * @return void
	 */
	private function __checkInstallation() {
		// Check the installation status
		if (ForumConfig::isInstalled()) {
			$this->redirect(array('action' => 'index', 'controller' => 'home', 'plugin' => 'forum'));
		}

		// If progress hasn't begun, redirect
		if ($this->action != 'check_database') {
			if (!$this->Session->check('Install')) {
				$this->redirect(array('action' => 'index'));
			}
		} else {
			if (!$this->Session->check('Install')) {
				$this->Session->write('Install', array());
			}
		}

		// Auto load DB
		if ($this->Session->check('Install.database')) {
			$this->DB =& ConnectionManager::getDataSource($this->Session->read('Install.database'));
		}
	}

	/**
	 * Check to see if the database tables have already been taken.
	 *
	 * @access private
	 * @param string $prefix
	 * @param boolean $userTable
	 * @return array
	 */
	private function __checkTables($prefix = '', $userTable = false) {
		$existent = $this->DB->listSources();
		$tables = array_flip($this->__getTables());

		foreach ($tables as $table => $value) {
			$tables[$table] = (in_array($prefix . $table, $existent) ? true : false);
		}

		return $tables;
	}

	/**
	 * Get a list of all databases, based on core/database.php.
	 *
	 * @access private
	 * @return array
	 */
	private function __getDatabases() {
		$dbConfig = new DATABASE_CONFIG();
		$databases = array();

		foreach ($dbConfig as $db => $params) {
			$databases[$db] = $db;
		}

		return $databases;
	}

	/**
	 * Load the installation settings.
	 *
	 * @access private
	 * @return array
	 */
	private function __getInstallation() {
		$path = FORUM_CONFIG .'install.ini';

		if (file_exists($path)) {
			return parse_ini_file($path);
		}

		return null;
	}

	/**
	 * Get a list of tables.
	 *
	 * @access private
	 * @param string $prefix
	 * @return array
	 */
	private function __getTables($prefix = null) {
		$tables = array('access', 'access_levels', 'forums', 'forum_categories', 'moderators', 'polls', 'poll_options', 'poll_votes', 'posts', 'reported', 'topics');

		if ($prefix) {
			foreach ($tables as &$table) {
				$table = $prefix . $table;
			}
		}

		return $tables;
	}

	/**
	 * Prefix the appropriate table.
	 *
	 * @access private
	 * @param string $table
	 * @return string
	 */
	private function __prefix($table) {
		$prefix = $this->Session->read('Install.prefix');
		
		if ($table == 'users') {
			if ($userTable = $this->Session->check('Install.user_table')) {
				return ($userTable == 1) ? $table : $prefix . $table;
			}
		}

		return $prefix . $table;
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
			case 'AppModel':	$path = FORUM_PLUGIN .'forum_app_model.php'; break;
			case 'UserModel':	$path = FORUM_PLUGIN .'models'. DS .'user.php'; break;
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
		$schemas = array('schema.sql', 'users_create.sql', 'users_alter.sql');

		foreach ($schemas as $schema) {
			if (file_exists(FORUM_SCHEMA . $schema)) {
				$contents = file_get_contents(FORUM_SCHEMA . $schema);
				$contents = String::insert($contents, array('prefix' => $prefix), array('before' => '{:', 'after' => '}'));
				$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

				$this->File = new File(FORUM_SCHEMA . 'parsed_'. $schema, true, 0777);
				$this->File->open('w', true);
				$this->File->write($contents);
				$this->File->close();
			}
		}
	}

	/**
	 * Rollback the installation and delete the created tables.
	 *
	 * @access private
	 * @param string $table
	 * @return void
	 */
	private function __rollback($table = null) {
		if (!$table) {
			$tables = $this->__getTables();

			foreach ($tables as $table) {
				$this->DB->execute('DROP TABLE `'. $this->__prefix($table) .'`;');
			}
		} else {
			$this->DB->execute('DROP TABLE `'. $this->__prefix($table) .'`;');
		}
	}

	/**
	 * Save the installation settings.
	 *
	 * @access private
	 * @return void
	 */
	private function __saveInstall() {
		$install = $this->Session->read('Install');
		$install['date'] = date('Y-m-d H:i:s');

		// Prepare settings for ini
		$settings = array();
		foreach ($install as $field => $value) {
			$settings[] = $field .' = "'. $value .'"';
		}

		// Save the ini file
		$path = FORUM_CONFIG .'install.ini';
		$contents = implode("\n", $settings);

		$this->File = new File($path, true, 0777);
		$this->File->open('w', true);
		$this->File->write($contents);
		$this->File->close();

		// Delete session
		$this->Session->delete('Install');
	}

	/**
	 * Save the routing info to the routes.php file.
	 *
	 * @access private
	 * @return void
	 */
	private function __saveRouting() {
		$path = CONFIGS .'routes.php';

		$contents = file_get_contents($path);
		$contents = str_replace('?>', '', $contents);
		$contents .= "\n\nRouter::parseExtensions('rss');";
		$contents .= "\nRouter::connect('/forum', array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index'));";

		$this->File = new File($path, true, 0777);
		$this->File->open('w', true);
		$this->File->write($contents);
		$this->File->close();
	}

	/**
	 * Before filter.
	 *
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Set the installation layout
		$this->layout = 'install';

		// The usual
		$this->set('menuTab', '');
	}

}