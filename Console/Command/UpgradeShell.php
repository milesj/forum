<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

Configure::write('debug', 2);
Configure::write('Cache.disable', true);

App::uses('ConnectionManager', 'Model');
App::uses('AppModel', 'Model');

class UpgradeShell extends Shell {

	/**
	 * Array of completed version upgrades.
	 *
	 * @var array
	 */
	public $complete = array();

	/**
	 * Available upgrade versions.
	 *
	 * @var array
	 */
	public $versions = array(
		'4.0' => 'ACL Restructure'
	);

	/**
	 * Execute upgrader!
	 *
	 * @return void
	 */
	public function main() {
		$this->out();
		$this->out('Plugin: Forum');
		$this->out('Version: ' . Configure::read('Forum.version'));
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');
		$this->out('Shell: Upgrade');
		$this->out();
		$this->out('This shell will upgrade versions and manage any database changes.');
		$this->out('Please do not skip versions, upgrade sequentially!');

		$this->upgrade();
	}

	/**
	 * List out all the available upgrade options.
	 */
	public function upgrade() {
		$this->hr(1);
		$this->out('Available versions:');
		$this->out();

		$versions = array();

		if ($this->versions) {
			foreach ($this->versions as $version => $title) {
				if (!in_array($version, $this->complete)) {
					$this->out(sprintf('[%s] %s', $version, $title));
					$versions[] = $version;
				}
			}
		}

		$this->out('[E]xit');
		$this->out();

		$versions[] = 'E';
		$version = strtoupper($this->in('Which version do you want to upgrade to?'));

		if ($version === 'E') {
			exit(0);
		} else {
			$this->hr(1);
			$this->out(sprintf('Upgrading to %s...', $version));
			$this->out();

			$method = 'to_' . str_replace('.', '', $version);

			if (method_exists($this, $method)) {
				$response = $this->{$method}();
			} else {
				$response = $this->_querySql($version);
			}

			if ($response) {
				$this->complete[] = $version;

				$this->out('Complete...');
				$this->finalize();
			}
		}
	}

	/**
	 * Output complete message and render versions again.
	 */
	public function finalize() {
		$this->out('You can now upgrade to another version or close the shell.');
		$this->upgrade();
	}

	/**
	 * Upgrade to 4.0.0.
	 */
	public function to_40() {
		$answer = strtoupper($this->in('This upgrade will delete the following tables after migration: settings, access, access_levels. Are you sure you want to continue?', array('Y', 'N')));

		if ($answer === 'N') {
			exit();
		}

		$Access = new AppModel(null, 'access', FORUM_DATABASE);
		$Access->alias = 'Access';
		$Access->tablePrefix = FORUM_PREFIX;
		$Access->bindModel(array('belongsTo' => array(
			'User' => array('className' => FORUM_USER)
		)));

		$AccessLevel = new AppModel(null, 'access_levels', FORUM_DATABASE);
		$AccessLevel->alias = 'AccessLevel';
		$AccessLevel->tablePrefix = FORUM_PREFIX;

		$Forum = ClassRegistry::init('Forum.Forum');
		$Acl = ClassRegistry::init('Forum.Access');

		// Create ACL request objects
		$this->out('Installing ACL...');

		$aclMap = $Acl->installAcl();
		$levelMap = array();

		foreach ($AccessLevel->find('all') as $level) {
			$id = $level['AccessLevel']['id'];

			if (!in_array($id, array(3, 4))) {
				continue;
			}

			if ($id == 3) {
				$alias = Configure::read('Forum.aroMap.superMod');
			} else {
				$alias = Configure::read('Forum.aroMap.admin');
			}

			$record = $Acl->getBySlug($alias);
			$levelMap[$id] = $record['Access']['id'];
		}

		// Create users
		$this->out('Migrating users...');

		foreach ($Access->find('all') as $user) {
			$Acl->add(array(
				'foreign_key' => $user['User']['id'],
				'parent_id' => $levelMap[$user['Access']['access_level_id']]
			));
		}

		// Migrate access levels
		$this->out('Migrating access relations...');

		$forums = $Forum->find('all', array(
			'conditions' => array('Forum.access_level_id !=' => 0)
		));

		if ($forums) {
			foreach ($forums as $forum) {
				$Forum->id = $forum['Forum']['id'];
				$Forum->save(array(
					'access_level_id' => $levelMap[$forum['Forum']['access_level_id']]
				), false);
			}
		}

		// Delete tables
		$this->out('Deleting old tables...');

		return $this->_querySql('4.0');
	}

	/**
	 * Execute the queries for the specific version SQL.
	 *
	 * @param string $version
	 * @return bool
	 */
	protected function _querySql($version) {
		sleep(1);

		$db = ConnectionManager::getDataSource(FORUM_DATABASE);
		$schema = FORUM_PLUGIN . 'Config/Schema/Upgrade/' . $version . '.sql';

		if (!file_exists($schema)) {
			$this->out(sprintf('Upgrade schema %s does not exist', $version));

			return false;
		}

		$sql = file_get_contents($schema);
		$sql = String::insert($sql, array('prefix' => FORUM_PREFIX), array('before' => '{', 'after' => '}'));
		$sql = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sql);

		foreach (explode(';', $sql) as $query) {
			$query = trim($query);

			if ($query !== '') {
				$db->execute($query);
			}
		}

		return true;
	}

}