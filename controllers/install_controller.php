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
		$this->__checkInstallation();

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
		$this->__checkInstallation();

		if (isset($this->data['database'])) {
			$this->Session->write('Install.database', $this->data['database']);
		} else {
			$this->redirect(array('action' => 'index'));
		}

		$this->Session->write('Install.prefix', $this->data['prefix']);
		$this->Session->write('Install.user_table', $this->data['user_table']);

		// Check database
		$this->DB = ConnectionManager::getDataSource($this->data['database']);

		if ($this->DB->isConnected()) {
			$tables = $this->__checkTables($this->data['prefix'], $this->data['user_table']);
			$takenTables = array();
			$prefixTables = array();

			if (!empty($tables)) {
				foreach ($tables as $table => $value) {
					$prefixTables[] = $this->__prefix($table);

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

		// Prepare SQL
		$this->__rewriteSql($this->Session->read('Install.prefix'));
		$path = dirname(__DIR__) . DS .'config'. DS .'schema'. DS;

		// Gather statements
		$schema = explode(";", file_get_contents($path . 'prepared_schema.sql'));

		if ($this->Session->read('Install.user_table') == 1) {
			$userSchema = array(file_get_contents($path . 'prepared_users_alter.sql'));
		} else {
			$userSchema = array(file_get_contents($path . 'prepared_users_create.sql'));
		}

		// Execute!
		$sqls = array_merge($schema, $userSchema);
		$total = count($this->__getTables());
		$executed = 0;

		foreach ($sqls as $sql) {
			$sql = trim($sql);
			
			if (!empty($sql) && $this->DB->execute($sql)) {
				$command = trim(substr($sql, 0, 6));
				
				if (($command == 'CREATE') || ($command == 'ALTER')) {
					$executed++;
				}
			}
		}

		// Check
		if ($executed != $total) {
			$this->__rollback();
			$this->Session->delete('Install');
		} else {
			$this->Session->write('Install.finished', true);
		}

		$this->pageTitle = 'Step 3: Create Tables';
		$this->set('database', $this->Session->read('Install.database'));
		$this->set('executed', $executed);
		$this->set('total', $total);
	}

	/**
	 * Finish the installation process.
	 *
	 * @access public
	 * @return void
	 */
	public function finished() {
		$this->__checkInstallation();
		
		if (!$this->Session->check('Install.finished')) {
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
	 * Patch your installation!
	 *
	 * @access public
	 * @return void
	 */
	public function patch() {
		if (!empty($this->data)) {
			$data = $this->data;
			$data['finished'] = true;
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
			$dbConfig = new DATABASE_CONFIG();
			$databases = array();
			foreach ($dbConfig as $db => $params) {
				$databases[$db] = $db;
			}

			$this->data['prefix'] = 'forum_';
			$this->set('databases', $databases);
		}
		
		$this->pageTitle = 'Patch Installation';
		$this->set('installed', $installed);
	}

	/**
	 * Upgrade to version 1.8!
	 *
	 * @access public
	 * @return void
	 */
	public function upgrade_1_8() {
		if (!ForumConfig::isInstalled()) {
			$this->redirect(array('action' => 'patch'));
		}

		$config = $this->__getInstallation();

		// Process
		if (!empty($this->data)) {
			$this->DB = ConnectionManager::getDataSource($config['database']);

			// Load 1.8 SQL and run
			$schema = dirname(__DIR__) . DS .'config'. DS .'schema'. DS .'upgrades'. DS .'1.8.sql';
			$contents = file_get_contents($schema);
			$contents = String::insert($contents, array('prefix' => $config['prefix']), array('before' => '{:', 'after' => '}'));
			$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

			/*$sqls = explode(';', $contents);
			foreach ($sqls as $sql) {
				$this->DB->execute(trim($sql));
			}
			
			// Apply slug to the tables
			$Topic = ClassRegistry::init('Forum.Topic');
			$Forum = ClassRegistry::init('Forum.Forum');
			$ForumCategory = ClassRegistry::init('Forum.ForumCategory');
			$slugSettings = array('label' => 'title', 'slug' => 'slug', 'separator' => '-', 'length' => 100, 'overwrite' => false);
			
			$topics = $Topic->find('all', array('contain' => false, 'callbacks' => false));
			if (!empty($topics)) {
				foreach ($topics as $topic) {
					$Topic->id = $topic['Topic']['id'];
					$Topic->save(array('slug' => $Topic->Behaviors->Sluggable->__slug($topic['Topic']['title'], $slugSettings)), false, array('slug'));
				}
			}

			$forums = $Forum->find('all', array('contain' => false, 'callbacks' => false));
			if (!empty($forums)) {
				foreach ($forums as $forum) {
					$Forum->id = $forum['Forum']['id'];
					$Forum->save(array('slug' => $Forum->Behaviors->Sluggable->__slug($forum['Forum']['title'], $slugSettings)), false, array('slug'));
				}
			}

			$forumsCats = $ForumCategory->find('all', array('contain' => false, 'callbacks' => false));
			if (!empty($forumsCats)) {
				foreach ($forumsCats as $forumCat) {
					$ForumCategory->id = $forumCat['ForumCategory']['id'];
					$ForumCategory->save(array('slug' => $ForumCategory->Behaviors->Sluggable->__slug($forumCat['ForumCategory']['title'], $slugSettings)), false, array('slug'));
				}
			}*/

			$this->set('upgraded', true);
		}

		$this->pageTitle = 'Upgrade to 1.8';
		$this->set('config', $config);
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
		if ($this->action != 'index') {
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
			if ($userTable == 1 && $table == 'users') {
				$tables['users'] = false;
			} else {
				$tables[$table] = (in_array($prefix . $table, $existent) ? true : false);
			}
		}

		return $tables;
	}

	/**
	 * Load the installation settings.
	 *
	 * @access private
	 * @return array
	 */
	private function __getInstallation() {
		$path = dirname(dirname(__FILE__)) . DS .'config'. DS .'install.ini';

		if (file_exists($path)) {
			return parse_ini_file($path);
		}

		return null;
	}

	/**
	 * Get a list of tables.
	 *
	 * @access private
	 * @return array
	 */
	private function __getTables() {
		return array('access', 'access_levels', 'forums', 'forum_categories', 'moderators', 'polls', 'poll_options', 'poll_votes', 'posts', 'reported', 'topics', 'users');
	}

	/**
	 * Prefix the appropriate table.
	 *
	 * @access private
	 * @param string $table
	 * @return string
	 */
	private function __prefix($table) {
		if ($table == 'users') {
			$table = (($this->Session->read('Install.user_table') == 1) ? $table : $this->Session->read('Install.prefix') . $table);
		} else {
			$table = $this->Session->read('Install.prefix') . $table;
		}

		return $table;
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
		$schemas = array('schema.sql', 'users_create.sql', 'users_alter.sql');

		foreach ($schemas as $schema) {
			if (file_exists($path . $schema)) {
				$contents = file_get_contents($path . $schema);
				$contents = String::insert($contents, array('prefix' => $prefix), array('before' => '{:', 'after' => '}'));
				$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

				$this->File = new File($path .'prepared_'. $schema, true, 0777);
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
	 * @return void
	 */
	private function __rollback() {
		$tables = $this->__getTables();

		foreach ($tables as $table) {
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
		$settings = array();

		foreach ($install as $field => $value) {
			if (!is_numeric($value)) {
				$value = '"'. $value .'"';
			}
			$settings[] = $field .' = '. $value;
		}

		$path = dirname(__DIR__) . DS .'config'. DS .'install.ini';
		$handle = fopen($path, 'w');
		fwrite($handle, implode("\n", $settings));
		fclose($handle);
		chmod($path, 0777);

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
		$this->Auth->allow('*');
		$this->set('menuTab', '');
	}

}