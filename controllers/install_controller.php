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

class InstallController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array();

	/**
	 * Primary installation and checking action.
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		if (!$this->Session->check('Forum.Install')) {
			$this->Session->write('Forum.Install', array('step' => 1));
		}

		$step = $this->Session->read('Forum.Install.step');
		$db =& ConnectionManager::getDataSource('milesj');

		// Step 1: Database Configuration
		if ($step == 1) {
			$this->pageTitle = 'Step 1: Database Configuration';
		}
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
		$result = $db->fetchAll('SHOW TABLES');
		$tables = array_flip(array('access', 'access_levels', 'forums', 'forum_categories', 'moderators', 'polls', 'poll_options', 'poll_votes', 'posts', 'reported', 'topics', 'users'));
		$existent = array();

		if (!empty($result)) {
			foreach ($result as $dbTables) {
				foreach ($dbTables as $tableList) {
					$table = array_values($tableList);
					$existent[] = $table[0];
				}
			}
		}

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

		// Set the installation layout
		$this->layout = 'install';

		// The usual
		$this->Auth->allow('*');
		$this->set('menuTab', '');
	}

}