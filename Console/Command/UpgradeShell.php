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

		$AccessLevel = new AppModel(null, 'access_levels', FORUM_DATABASE);
		$AccessLevel->alias = 'AccessLevel';
		$AccessLevel->tablePrefix = FORUM_PREFIX;

		$Permission = ClassRegistry::init('Permission');
		$Aco = ClassRegistry::init('Aco');
		$Aro = ClassRegistry::init('Aro');

		// Create ACL request objects
		$this->out('Creating AROs...');
		$aroMap = array();
		$aroAliases = array();

		foreach ($AccessLevel->find('all') as $level) {
			$id = $level['AccessLevel']['id'];
			$alias = 'forum.';

			if ($id <= 2) {
				continue;
			}

			switch ($id) {
				case 3:
					$alias .= 'superMod';
				break;
				case 4:
					$alias .= 'admin';
				break;
				default:
					$alias .= lcfirst(Inflector::camelize($level['AccessLevel']['title']));
				break;
			}

			// Check to see if the ARO already exists
			$result = $Aro->find('first', array(
				'conditions' => array('alias' => $alias),
				'recursive' => -1
			));

			if ($result) {
				$aroMap[$id] = $result['Aro']['id'];

			// Else create a new record
			} else {
				$Aro->create();
				$Aro->save(array('alias' => $alias));

				$aroMap[$id] = $Aro->id;
			}

			$aroAliases[] = $alias;
		}

		// Create ACL control objects
		$this->out('Creating ACOs...');
		$acoMap = array();
		$acoAliases = array();

		foreach (array('admin', 'stations', 'topics', 'posts', 'polls') as $type) {
			$alias = 'forum.'. $type;

			// Check to see if the ACO already exists
			$result = $Aco->find('first', array(
				'conditions' => array('alias' => $alias),
				'recursive' => -1
			));

			if ($result) {
				$acoMap[] = $result['Aco']['id'];

			// Else create a new record
			} else {
				$Aco->create();
				$Aco->save(array('alias' => $alias));

				$acoMap[] = $Aco->id;
			}

			$acoAliases[] = $alias;
		}

		// Allow ACOs to AROs
		$this->out('Creating permissions...');
		foreach ($aroAliases as $ro) {
			foreach ($acoAliases as $co) {
				$Permission->allow($ro, $co);
			}
		}

		// Create users
		$this->out('Migrating users...');

		$Access->bindModel(array('belongsTo' => array(FORUM_USER)));
		$users = $Access->find('all', array(
			'recursive' => 0
		));

		foreach ($users as $user) {
			$parent_id = $aroMap[$user['Access']['access_level_id']];
			$user_id = $user['User']['id'];

			$count = $Aro->find('count', array(
				'conditions' => array(
					'parent_id' => $parent_id,
					'foreign_key' => $user_id
				)
			));

			if (!$count) {
				$Aro->create();
				$Aro->save(array(
					'alias' => $user['User'][Configure::read('Forum.userMap.username')],
					'parent_id' => $parent_id,
					'model' => FORUM_USER,
					'foreign_key' => $user_id
				));
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