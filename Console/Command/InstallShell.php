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
Configure::load('Forum.config');

App::uses('ConnectionManager', 'Model');
App::uses('User', 'Model');
App::uses('Security', 'Utility');
App::uses('Sanitize', 'Utility');
App::uses('Validation', 'Utility');

define('FORUM_PLUGIN', dirname(dirname(dirname(__FILE__))) . DS);
define('FORUM_SCHEMA', FORUM_PLUGIN . 'config' . DS . 'schema' . DS);

include_once APP . 'Config' . DS . 'database.php';

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
		'user_id' => '',
		'username' => '',
		'password' => '',
		'email' => ''
	);

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
		$this->out('Version: '. $this->config['version']);
		$this->out('Copyright: Miles Johnson, 2010-'. date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');
		$this->out('Shell: Installer');
		$this->out();
		$this->out('This shell installs the forum plugin by creating the required database tables,');
		$this->out('setting up the admin user, applying necessary table prefixes, and more.');
		
		$this->hr(1);
		$this->out('Installation Steps:');
		$this->out();
		$this->steps(0);
		$this->out('This installer will drop all forum specific database tables (not users) when executed!');

		if (strtoupper($this->in('Continue?', array('Y', 'N'))) == 'N') {
			return;
		}

		$this->steps(1);
		$this->tablePrefix();
		$this->hr(1);
		
		$this->steps(2);
		$this->databaseConfig();
		$this->hr(1);
		
		$this->steps(3);
		$this->checkStatus();
		$this->hr(1);
		
		$this->steps(4);
		$this->createTables();
		$this->overrideAppModel();
		$this->hr(1);
		
		$this->steps(5);
		$this->setupAdmin();
		$this->hr(1);
		
		$this->steps(6);
		$this->finalize();
		$this->hr(1);
	}
	
	/**
	 * Table of contents.
	 * 
	 * @access public
	 * @param int $state 
	 * @return void
	 */
	public function steps($state = 0) {
		$steps = array(
			'Table Prefix',
			'Database Configuration',
			'Check Installation Status',
			'Create Database Tables',
			'Create Administator',
			'Finalize Installation'
		);
		
		foreach ($steps as $i => $step) {
			$index = ($i + 1);
			
			$this->out('['. (($index < $state) ? 'x' : $index) .'] '. $step);
		}
		
		$this->out();
	}
	
	/**
	 * Set the table prefix to use.
	 * 
	 * @access public
	 * @return void
	 */
	public function tablePrefix() {
		$prefix = $this->in('What table prefix would you like to use?');
		
		if (empty($prefix)) {
			$this->out('Please provide a table prefix, I recommend "forum".');
			$this->tablePrefix();
			return;
				
		} else {
			$prefix = trim($prefix, '_') .'_';
			$this->out(sprintf('You have chosen the prefix: %s', $prefix));
		}

		$answer = strtoupper($this->in('Is this correct?', array('Y', 'N')));
		
		if ($answer == 'Y') {
			$this->install['prefix'] = $prefix;
		} else {
			$this->tablePrefix();
		}
	}
	
	/**
	 * Set the database to use.
	 * 
	 * @access public
	 * @return void
	 */
	public function databaseConfig() {
		$dbs = new DATABASE_CONFIG();
		$list = array();
		$counter = 1;
		
		$this->out('Possible database configurations:');
		
		foreach ($dbs as $db => $config) {
			$this->out('['. $counter .'] '. $db);
			$list[$counter] = $db;
			$counter++;
		}
		
		$this->out();
		
		$answer = strtoupper($this->in('Which database should the tables be created in?', array_keys($list)));
		
		if (isset($list[$answer])) {
			$this->install['database'] = $list[$answer];
			$this->db = ConnectionManager::getDataSource($this->install['database']);
		} else {
			$this->databaseConfig();
		}
	}
	
	/**
	 * Check the database status before installation.
	 * 
	 * @access public
	 * @return void
	 */
	public function checkStatus() {
		// Check connection
		if (!$this->db->isConnected()) {
			$this->out(sprintf('Error: Database connection for %s failed!', $this->install['database']));
			return;
		}
		
		// Check the users tables
		$tables = $this->db->listSources();

		if (!in_array('users', $tables)) {
			$this->out(sprintf('Error: No users table was found in %s.', $this->install['database']));
			return;
		}
		
		$this->out('Installation status good, proceeding...');
	}
	
	/**
	 * Create the database tables based off the schemas.
	 * 
	 * @access public
	 * @return void
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

					if ($command == 'CREATE' || $command == 'ALTER') {
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
		} else {
			$this->out('Tables created successfully...');
		}
	}
	
	/**
	 * Setup the admin user.
	 * 
	 * @access public
	 * @return void
	 */
	public function setupAdmin() {
		$answer = strtoupper($this->in('Would you like to [c]reate a new user, or use an [e]xisting user?', array('C', 'E')));

		// New User
		if ($answer == 'C') {
			$this->install['username'] = $this->_newUser('username');
			$this->install['password'] = $this->_newUser('password');
			$this->install['email'] = $this->_newUser('email');
			
			$result = $this->db->execute(sprintf("INSERT INTO `users` (`%s`, `%s`, `%s`, `%s`) VALUES (%s, %s, %s, %s);",
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
				$this->setupAdmin();
			}
			
		// Old User
		} else if ($answer == 'E') {
			$this->install['user_id'] = $this->_oldUser();
			
		// Redo
		} else {
			$this->setupAdmin();
		}
		
		$result = $this->db->execute(sprintf("INSERT INTO `%saccess` (`access_level_id`, `user_id`, `created`) VALUES (4, %d, NOW());", (string) $this->install['prefix'], (int) $this->install['user_id']));

		if (!$result) {
			$this->out('An error occured while granting administrator access.');
			$this->setupAdmin();
		}
	}
	
	/**
	 * Rewrite specific AppModel variables.
	 * 
	 * @access public
	 * @return void
	 */
	public function overrideAppModel() {
		$appModel = file_get_contents(FORUM_PLUGIN . 'forum_app_model.php');
		$appModel = preg_replace('/public \$tablePrefix = \'(.*?)\';/', 'public \$tablePrefix = \''. $this->install['prefix'] .'\';', $appModel);
		$appModel = preg_replace('/public \$useDbConfig = \'(.*?)\';/', 'public \$useDbConfig = \''. $this->install['database'] .'\';', $appModel);
		
		file_put_contents(FORUM_PLUGIN . 'forum_app_model.php', $appModel);
	}
	
	/**
	 * Finalize the installation, woop woop.
	 * 
	 * @access public
	 * @return void
	 */
	public function finalize() {
		$this->hr(1);
		$this->out('Forum installation complete! Your admin credentials:');
		$this->out();
		$this->out(sprintf('Username: %s', $this->install['username']));
		$this->out(sprintf('Email: %s', $this->install['email']));
		$this->out();
		$this->out('Please read the documentation for further configuration instructions.');
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
				
				if (empty($username)) {
					$username = $this->_newUser($mode);
				} else {
					$result = $this->db->fetchRow(sprintf("SELECT COUNT(*) AS `count` FROM `users` AS `User` WHERE `%s` = %s", $this->config['userMap']['username'], $this->db->value($username)));
					
					if ($this->db->hasResult() && $result[0]['count']) {
						$this->out('Username already exists, please try again.');
						$username = $this->_newUser($mode);
					}
				}
				
				return $username;
			break;
			
			case 'password':
				$password = trim($this->in('Password:'));
				
				if (empty($password)) {
					$password = $this->_newUser($mode);
				}
				
				return $password;
			break;
			
			case 'email':
				$email = trim($this->in('Email:'));
				
				if (empty($email)) {
					$email = $this->_newUser($mode);
					
				} else if (!Validation::email($email)) {
					$this->out('Invalid email address, please try again.');
					$email = $this->_newUser($mode);
					
				} else {
					$result = $this->db->fetchRow(sprintf("SELECT COUNT(*) AS `count` FROM `users` AS `User` WHERE `%s` = %s", $this->config['userMap']['email'], $this->db->value($email)));

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
		
		if (empty($user_id) || !is_numeric($user_id)) {
			$user_id = $this->_oldUser();
		
		} else {
			$result = $this->db->fetchRow(sprintf("SELECT * FROM `users` AS `User` WHERE `id` = %d LIMIT 1", (int) $user_id));

			if (empty($result)) {
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