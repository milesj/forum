<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

Configure::write('debug', 2);
Configure::write('Cache.disable', true);

App::uses('ConnectionManager', 'Model');
App::uses('Security', 'Utility');
App::uses('Sanitize', 'Utility');
App::uses('Validation', 'Utility');

config('database');

class InstallShell extends Shell {

	/**
	 * Installer configuration.
	 *
	 * @var array
	 */
	public $install = array(
		'table' => 'users',
		'user_id' => '',
		'username' => '',
		'password' => '',
		'email' => '',
		'acl_admin' => 0
	);

	/**
	 * DB Instance.
	 *
	 * @var DboSource
	 */
	public $db;

	/**
	 * Execute installer!
	 *
	 * @return void
	 */
	public function main() {
		$this->out();
		$this->out('Plugin: Forum v'. Configure::read('Forum.version'));
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');
		$this->out();
		$this->out('This shell installs the forum plugin by creating the required database tables,');
		$this->out('setting up the admin user, applying necessary table prefixes, and more.');

		$this->hr(1);
		$this->out('Installation Steps:');

		// Begin installation
		$this->db = ConnectionManager::getDataSource(FORUM_DATABASE);
		$this->steps(1);

		if ($this->usersTable()) {
			$this->steps(2);

			if ($this->checkStatus()) {
				$this->steps(3);

				if ($this->setupAcl()) {
					$this->steps(4);

					if ($this->createTables()) {
						$this->steps(5);

						if ($this->setupAdmin()) {
							$this->steps(6);
							$this->finalize();
						}
					}
				}
			}
		}
	}

	/**
	 * Table of contents.
	 *
	 * @param int $state
	 * @return void
	 */
	public function steps($state = 0) {
		$this->hr(1);

		$steps = array(
			'Users Table',
			'Check Installation Status',
			'Setup ACL',
			'Create Database Tables',
			'Create Administrator',
			'Finalize Installation'
		);

		foreach ($steps as $i => $step) {
			$index = ($i + 1);

			if ($index < $state) {
				$this->out('[x] ' . $step);
			} else {
				$this->out('[' . $index . '] <comment>' . $step . '</comment>');
			}
		}

		$this->out();
	}

	/**
	 * Grab the users table.
	 *
	 * @return bool
	 */
	public function usersTable() {
		$table = $this->in('<question>What is the name of your users table?</question>');

		if (!$table) {
			$this->out('<error>Please provide a users table</error>');

			return $this->usersTable();

		} else {
			$table = trim($table);
			$this->out(sprintf('You have chosen the table: %s', $table));
		}

		$answer = strtoupper($this->in('<question>Is this correct?</question>', array('Y', 'N')));

		if ($answer === 'Y') {
			$this->install['table'] = $table;
		} else {
			return $this->usersTable();
		}

		return true;
	}

	/**
	 * Check the database status before installation.
	 *
	 * @return bool
	 */
	public function checkStatus() {
		if (!$this->db->isConnected()) {
			$this->out(sprintf('<error>Database connection for %s failed!</error>', FORUM_DATABASE));

			return false;
		}

		// Check the required tables
		$tables = $this->db->listSources();
		$checkFor = array($this->install['table'], 'aros', 'acos', 'aros_acos');

		$this->out(sprintf('The following tables are required: %s', implode(', ', $checkFor)));
		$this->out('<info>Checking tables...</info>');

		foreach ($checkFor as $table) {
			if (!in_array($table, $tables)) {
				$this->out(sprintf('<error>No %s table was found in %s</error>', $table, FORUM_DATABASE));

				return false;
			}
		}

		$this->out('<info>Installation status good, proceeding...</info>');

		return true;
	}

	/**
	 * Create all the ACL records.
	 */
	public function setupAcl() {
		$this->out('<info>Creating ACL records...</info>');

		$admin = Configure::read('Admin.aliases.administrator');
		$acl = ClassRegistry::init('Forum.Access')->installAcl();

		foreach ($acl['aro'] as $id => $alias) {
			if ($alias === $admin) {
				$this->install['acl_admin'] = $id;
			}
		}

		$this->out('<info>ACL setup successfully...</info>');

		return true;
	}

	/**
	 * Create the database tables based off the schemas.
	 *
	 * @return bool
	 */
	public function createTables() {
		$answer = strtoupper($this->in('<question>Existing tables will be deleted, continue?</question>', array('Y', 'N')));

		if ($answer === 'N') {
			exit();
		}

		$schemas = glob(FORUM_PLUGIN . 'Config/Schema/*.sql');
		$executed = 0;
		$total = count($schemas);
		$tables = array();

		foreach ($schemas as $schema) {
			$contents = file_get_contents($schema);
			$contents = String::insert($contents, array('prefix' => FORUM_PREFIX), array('before' => '{', 'after' => '}'));
			$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

			$queries = explode(';', $contents);
			$tables[] = FORUM_PREFIX . str_replace('.sql', '', basename($schema));

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
			$this->out('<error>Failed to create database tables!</error>');
			$this->out('Rolling back and dropping any created tables');

			foreach ($tables as $table) {
				$this->db->execute(sprintf('DROP TABLE `%s`;', $table));
			}

			return false;
		} else {
			$this->out('<info>Tables created successfully...</info>');
		}

		return true;
	}

	/**
	 * Setup the admin user.
	 *
	 * @return bool
	 */
	public function setupAdmin() {
		$answer = strtoupper($this->in('<question>Would you like to [c]reate a new user, or use an [e]xisting user?</question>', array('C', 'E')));
		$userMap = Configure::read('User.fieldMap');
		$statusMap = Configure::read('User.statusMap');

		// New User
		if ($answer === 'C') {
			$this->install['username'] = $this->_newUser('username');
			$this->install['password'] = $this->_newUser('password');
			$this->install['email'] = $this->_newUser('email');

			$result = $this->db->execute(sprintf("INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`) VALUES (%s, %s, %s, %s);",
				$this->install['table'],
				$userMap['username'],
				$userMap['password'],
				$userMap['email'],
				$userMap['status'],
				$this->db->value(Sanitize::clean($this->install['username'])),
				$this->db->value(Security::hash($this->install['password'], null, true)),
				$this->db->value($this->install['email']),
				$this->db->value($statusMap['active'])
			));

			if ($result) {
				$this->install['user_id'] = $this->db->lastInsertId();
			} else {
				$this->out('<error>An error has occurred while creating the user</error>');

				return $this->setupAdmin();
			}

		// Old User
		} else if ($answer === 'E') {
			$this->install['user_id'] = $this->_oldUser();

		// Redo
		} else {
			return $this->setupAdmin();
		}

		// Give ACL
		$result = ClassRegistry::init('Forum.Access')->add(array(
			'parent_id' => $this->install['acl_admin'],
			'foreign_key' => $this->install['user_id']
		));

		if (!$result) {
			$this->out('<error>An error occurred while granting administrator access</error>');

			return $this->setupAdmin();
		}

		return true;
	}

	/**
	 * Finalize the installation, woop woop.
	 *
	 * @return void
	 */
	public function finalize() {
		$this->hr(1);
		$this->out('Forum installation complete! Your admin credentials:');
		$this->out();
		$this->out(sprintf('<comment>Username:</comment> %s', $this->install['username']));
		$this->out(sprintf('<comment>Email:</comment> %s', $this->install['email']));
		$this->out();
		$this->out('Please read the documentation for further instructions:');
		$this->out('http://milesj.me/code/cakephp/forum');
		$this->hr(1);
	}

	/**
	 * Gather all the data for creating a new user.
	 *
	 * @param string $mode
	 * @return string
	 */
	protected function _newUser($mode) {
		$userMap = Configure::read('User.fieldMap');

		switch ($mode) {
			case 'username':
				$username = trim($this->in('<question>Username:</question>'));

				if (!$username) {
					$username = $this->_newUser($mode);
				} else {
					$result = $this->db->fetchRow(sprintf("SELECT COUNT(*) AS `count` FROM `%s` AS `User` WHERE `%s` = %s",
						$this->install['table'],
						$userMap['username'],
						$this->db->value($username)
					));

					if ($this->db->hasResult() && $result[0]['count']) {
						$this->out('<error>Username already exists, please try again</error>');
						$username = $this->_newUser($mode);
					}
				}

				return $username;
			break;

			case 'password':
				$password = trim($this->in('<question>Password:</question>'));

				if (!$password) {
					$password = $this->_newUser($mode);
				}

				return $password;
			break;

			case 'email':
				$email = trim($this->in('<question>Email:</question>'));

				if (!$email) {
					$email = $this->_newUser($mode);

				} else if (!Validation::email($email)) {
					$this->out('<error>Invalid email address, please try again</error>');
					$email = $this->_newUser($mode);

				} else {
					$result = $this->db->fetchRow(sprintf("SELECT COUNT(*) AS `count` FROM `%s` AS `User` WHERE `%s` = %s",
						$this->install['table'],
						$userMap['email'],
						$this->db->value($email)
					));

					if ($this->db->hasResult() && $result[0]['count']) {
						$this->out('<error>Email already exists, please try again</error>');
						$email = $this->_newUser($mode);
					}
				}

				return $email;
			break;
		}

		return null;
	}

	/**
	 * Use an old user as an admin.
	 *
	 * @return string
	 */
	protected function _oldUser() {
		$user_id = trim($this->in('<question>User ID:</question>'));
		$userMap = Configure::read('User.fieldMap');

		if (!$user_id || !is_numeric($user_id)) {
			$user_id = $this->_oldUser();

		} else {
			$result = $this->db->fetchRow(sprintf("SELECT * FROM `%s` AS `User` WHERE `id` = %d LIMIT 1",
				$this->install['table'],
				$user_id
			));

			if (!$result) {
				$this->out('<error>User ID does not exist, please try again</error>');
				$user_id = $this->_oldUser();

			} else {
				$this->install['username'] = $result['User'][$userMap['username']];
				$this->install['password'] = $result['User'][$userMap['password']];
				$this->install['email'] = $result['User'][$userMap['email']];
			}
		}

		return $user_id;
	}

}