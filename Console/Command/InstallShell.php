<?php
/**
 * Forum - InstallShell
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

Configure::write('debug', 2);
Configure::write('Cache.disable', true);

App::uses('ConnectionManager', 'Model');
App::uses('Security', 'Utility');
App::uses('Sanitize', 'Utility');
App::uses('Validation', 'Utility');

define('FORUM_PLUGIN', dirname(dirname(dirname(__FILE__))) . '/');
define('FORUM_SCHEMA', FORUM_PLUGIN . 'Config/Schema/');
config('database');

class InstallShell extends Shell {

	/**
	 * Plugin configuration.
	 *
	 * @access public
	 * @var array
	 */
	public $config = array();

	/**
	 * Installer configuration.
	 *
	 * @access public
	 * @var array
	 */
	public $install = array(
		'prefix' => 'forum_',
		'database' => 'default',
		'table' => 'users',
		'user_id' => '',
		'username' => '',
		'password' => '',
		'email' => ''
	);

	/**
	 * DB Instance.
	 *
	 * @access public
	 * @var DataSource
	 */
	public $db;

	/**
	 * Execute installer!
	 *
	 * @access public
	 * @return void
	 */
	public function main() {
		$this->config = Configure::read('Forum');

		$this->out();
		$this->out('Plugin: Forum');
		$this->out('Version: ' . $this->config['version']);
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');
		$this->out('Shell: Installer');
		$this->out();
		$this->out('This shell installs the forum plugin by creating the required database tables,');
		$this->out('setting up the admin user, applying necessary table prefixes, and more.');

		$this->hr(1);
		$this->out('Installation Steps:');
		$this->out();
		$this->steps(1);

		if ($this->usersTable()) {
			$this->steps(2);

			if ($this->tablePrefix()) {
				$this->steps(3);

				if ($this->databaseConfig()) {
					$this->steps(4);

					if ($this->checkStatus()) {
						$this->steps(5);

						if ($this->createTables()) {
							$this->overrideAppModel();
							$this->steps(6);

							if ($this->setupAdmin()) {
								$this->steps(7);
								$this->finalize();
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Table of contents.
	 *
	 * @access public
	 * @param int $state
	 * @return void
	 */
	public function steps($state = 0) {
		$this->hr(1);

		$steps = array(
			'Users Table',
			'Table Prefix',
			'Database Configuration',
			'Check Installation Status',
			'Create Database Tables',
			'Create Administrator',
			'Finalize Installation'
		);

		foreach ($steps as $i => $step) {
			$index = ($i + 1);

			$this->out('[' . (($index < $state) ? 'x' : $index) . '] ' . $step);
		}

		$this->out();
	}

	/**
	 * Grab the users table.
	 *
	 * @access public
	 * @return boolean
	 */
	public function usersTable() {
		$table = $this->in('What is the name of your users table?');

		if (!$table) {
			$this->out('Please provide a users table.');

			return $this->usersTable();

		} else {
			$table = trim($table);
			$this->out(sprintf('You have chosen the table: %s', $table));
		}

		$answer = strtoupper($this->in('Is this correct?', array('Y', 'N')));

		if ($answer === 'Y') {
			$this->install['table'] = $table;
		} else {
			return $this->usersTable();
		}

		return true;
	}

	/**
	 * Set the table prefix to use.
	 *
	 * @access public
	 * @return boolean
	 */
	public function tablePrefix() {
		$prefix = $this->in('What table prefix would you like to use?');

		if (!$prefix) {
			$this->out('Please provide a table prefix, I recommend "forum".');

			return $this->tablePrefix();

		} else {
			$prefix = trim($prefix, '_') . '_';
			$this->out(sprintf('You have chosen the prefix: %s', $prefix));
		}

		$answer = strtoupper($this->in('Is this correct?', array('Y', 'N')));

		if ($answer === 'Y') {
			$this->install['prefix'] = $prefix;
		} else {
			return $this->tablePrefix();
		}

		return true;
	}

	/**
	 * Set the database to use.
	 *
	 * @access public
	 * @return boolean
	 */
	public function databaseConfig() {
		$dbs = new DATABASE_CONFIG();
		$list = array();
		$counter = 1;

		$this->out('Possible database configurations:');

		foreach ($dbs as $db => $config) {
			$this->out('[' . $counter . '] ' . $db);
			$list[$counter] = $db;
			$counter++;
		}

		$this->out();

		$answer = strtoupper($this->in('Which database should the tables be created in?', array_keys($list)));

		if (isset($list[$answer])) {
			$this->install['database'] = $list[$answer];
			$this->db = ConnectionManager::getDataSource($this->install['database']);

		} else {
			return $this->databaseConfig();
		}

		return true;
	}

	/**
	 * Check the database status before installation.
	 *
	 * @access public
	 * @return boolean
	 */
	public function checkStatus() {
		// Check connection
		if (!$this->db->isConnected()) {
			$this->out(sprintf('Error: Database connection for %s failed!', $this->install['database']));

			return false;
		}

		// Check the users tables
		$tables = $this->db->listSources();

		if (!in_array($this->install['table'], $tables)) {
			$this->out(sprintf('Error: No %s table was found in %s.', $this->install['table'], $this->install['database']));

			return false;
		}

		$this->out('Installation status good, proceeding...');

		return true;
	}

	/**
	 * Create the database tables based off the schemas.
	 *
	 * @access public
	 * @return boolean
	 */
	public function createTables() {
		$schemas = glob(FORUM_SCHEMA . '*.sql');
		$executed = 0;
		$total = count($schemas);
		$tables = array();

		foreach ($schemas as $schema) {
			$contents = file_get_contents($schema);
			$contents = String::insert($contents, array('prefix' => $this->install['prefix']), array('before' => '{', 'after' => '}'));
			$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

			$queries = explode(';', $contents);
			$tables[] = $this->install['prefix'] . str_replace('.sql', '', basename($schema));

			foreach ($queries as $query) {
				$query = trim($query);

				if ($query !== '' && $this->db->execute($query)) {
					$command = trim(substr($query, 0, 6));

					if ($command === 'CREATE' || $command === 'ALTER') {
						$executed++;
					}
				}
			}
		}

		if ($executed != $total) {
			$this->out('Error: Failed to create database tables!');
			$this->out('Rolling back and dropping any created tables.');

			foreach ($tables as $table) {
				$this->db->execute(sprintf('DROP TABLE `%s`;', $table));
			}

			return false;
		} else {
			$this->out('Tables created successfully...');
		}

		return true;
	}

	/**
	 * Setup the admin user.
	 *
	 * @access public
	 * @return boolean
	 */
	public function setupAdmin() {
		$answer = strtoupper($this->in('Would you like to [c]reate a new user, or use an [e]xisting user?', array('C', 'E')));

		// New User
		if ($answer === 'C') {
			$this->install['username'] = $this->_newUser('username');
			$this->install['password'] = $this->_newUser('password');
			$this->install['email'] = $this->_newUser('email');

			$result = $this->db->execute(sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`) VALUES (%s, %s, %s, %s);",
				$this->install['table'],
				$this->config['userMap']['username'],
				$this->config['userMap']['password'],
				$this->config['userMap']['email'],
				$this->config['userMap']['status'],
				$this->db->value(Sanitize::clean($this->install['username'])),
				$this->db->value(Security::hash($this->install['password'], null, true)),
				$this->db->value($this->install['email']),
				$this->db->value($this->config['statusMap']['active'])
			));

			if ($result) {
				$this->install['user_id'] = $this->db->lastInsertId();
			} else {
				$this->out('An error has occured while creating the user.');

				return $this->setupAdmin();
			}

		// Old User
		} else if ($answer === 'E') {
			$this->install['user_id'] = $this->_oldUser();

		// Redo
		} else {
			return $this->setupAdmin();
		}

		$result = $this->db->execute(sprintf("INSERT INTO `%saccess` (`access_level_id`, `user_id`, `created`) VALUES (4, %d, NOW());",
			$this->install['prefix'],
			$this->install['user_id']
		));

		if (!$result) {
			$this->out('An error occured while granting administrator access.');

			return $this->setupAdmin();
		}

		return true;
	}

	/**
	 * Rewrite specific AppModel variables.
	 *
	 * @access public
	 * @return void
	 */
	public function overrideAppModel() {
		$appModel = file_get_contents(FORUM_PLUGIN . 'Model/ForumAppModel.php');
		$appModel = preg_replace('/public \$tablePrefix = \'(.*?)\';/', 'public \$tablePrefix = \'' . $this->install['prefix'] . '\';', $appModel);
		$appModel = preg_replace('/public \$useDbConfig = \'(.*?)\';/', 'public \$useDbConfig = \'' . $this->install['database'] . '\';', $appModel);

		file_put_contents(FORUM_PLUGIN . 'Model/ForumAppModel.php', $appModel);
	}

	/**
	 * Finalize the installation, woop woop.
	 *
	 * @access public
	 * @return void
	 */
	public function finalize() {
		$ini = sprintf("; Forum installed on %s", date('Y-m-d H:i:s')) . PHP_EOL;

		foreach (array('prefix', 'database', 'table') as $key) {
			$ini .= $key . ' = "' . $this->install[$key] . '"' . PHP_EOL;
		}

		file_put_contents(FORUM_PLUGIN  . 'Config/install.ini', $ini);

		$this->hr(1);
		$this->out('Forum installation complete! Your admin credentials:');
		$this->out();
		$this->out(sprintf('Username: %s', $this->install['username']));
		$this->out(sprintf('Email: %s', $this->install['email']));
		$this->out();
		$this->out('Please read the documentation for further configuration instructions.');
		$this->hr(1);
	}

	/**
	 * Gather all the data for creating a new user.
	 *
	 * @access protected
	 * @param string $mode
	 * @return string
	 */
	protected function _newUser($mode) {
		switch ($mode) {
			case 'username':
				$username = trim($this->in('Username:'));

				if (!$username) {
					$username = $this->_newUser($mode);
				} else {
					$result = $this->db->fetchRow(sprintf("SELECT COUNT(*) AS `count` FROM `%s` AS `User` WHERE `%s` = %s",
						$this->install['table'],
						$this->config['userMap']['username'],
						$this->db->value($username)
					));

					if ($this->db->hasResult() && $result[0]['count']) {
						$this->out('Username already exists, please try again.');
						$username = $this->_newUser($mode);
					}
				}

				return $username;
			break;

			case 'password':
				$password = trim($this->in('Password:'));

				if (!$password) {
					$password = $this->_newUser($mode);
				}

				return $password;
			break;

			case 'email':
				$email = trim($this->in('Email:'));

				if (!$email) {
					$email = $this->_newUser($mode);

				} else if (!Validation::email($email)) {
					$this->out('Invalid email address, please try again.');
					$email = $this->_newUser($mode);

				} else {
					$result = $this->db->fetchRow(sprintf("SELECT COUNT(*) AS `count` FROM `%s` AS `User` WHERE `%s` = %s",
						$this->install['table'],
						$this->config['userMap']['email'],
						$this->db->value($email)
					));

					if ($this->db->hasResult() && $result[0]['count']) {
						$this->out('Email already exists, please try again.');
						$email = $this->_newUser($mode);
					}
				}

				return $email;
			break;
		}
	}

	/**
	 * Use an old user as an admin.
	 *
	 * @access protected
	 * @return string
	 */
	protected function _oldUser() {
		$user_id = trim($this->in('User ID:'));

		if (!$user_id || !is_numeric($user_id)) {
			$user_id = $this->_oldUser();

		} else {
			$result = $this->db->fetchRow(sprintf("SELECT * FROM `%s` AS `User` WHERE `id` = %d LIMIT 1",
				$this->install['table'],
				$user_id
			));

			if (!$result) {
				$this->out('User ID does not exist, please try again.');
				$user_id = $this->_oldUser();

			} else {
				$this->install['username'] = $result['User'][$this->config['userMap']['username']];
				$this->install['password'] = $result['User'][$this->config['userMap']['password']];
				$this->install['email'] = $result['User'][$this->config['userMap']['email']];
			}
		}

		return $user_id;
	}

}