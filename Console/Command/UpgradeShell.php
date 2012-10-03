<?php
/**
 * Forum - UpgradeShell
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

Configure::write('debug', 2);
Configure::write('Cache.disable', true);

App::uses('ConnectionManager', 'Model');

define('FORUM_PLUGIN', dirname(dirname(dirname(__FILE__))) . '/');
define('FORUM_SCHEMA', FORUM_PLUGIN . 'Config/Schema/Upgrade/');

class UpgradeShell extends Shell {

	/**
	 * Plugin configuration.
	 *
	 * @access public
	 * @var array
	 */
	public $config = array();

	/**
	 * Array of completed version upgrades.
	 *
	 * @access public
	 * @var array
	 */
	public $complete = array();

	/**
	 * Upgrade configuration.
	 *
	 * @access public
	 * @var array
	 */
	public $upgrade = array(
		'prefix' => 'forum_',
		'database' => 'default'
	);

	/**
	 * Available upgrade versions.
	 *
	 * @access public
	 * @var array
	 */
	public $versions = array(
		'2.2' => 'Subscriptions'
	);

	/**
	 * Execute upgrader!
	 *
	 * @access public
	 * @return void
	 */
	public function main() {
		$this->config = Configure::read('Forum');
		$this->upgrade = parse_ini_file(FORUM_PLUGIN  . 'Config/install.ini', true);

		// Begin
		$this->out();
		$this->out('Plugin: Forum');
		$this->out('Version: ' . $this->config['version']);
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

			$this->_querySql($version);
			$this->complete[] = $version;

			$this->out('Complete...');
			$this->finalize();
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
	 * Execute the queries for the specific version SQL.
	 *
	 * @access protected
	 * @param string $version
	 * @return void
	 */
	protected function _querySql($version) {
		sleep(1);

		$db = ConnectionManager::getDataSource($this->upgrade['database']);
		$schema = FORUM_SCHEMA . $version . '.sql';

		$sql = file_get_contents($schema);
		$sql = String::insert($sql, array('prefix' => $this->upgrade['prefix']), array('before' => '{', 'after' => '}'));
		$sql = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $sql);

		foreach (explode(';', $sql) as $query) {
			$query = trim($query);

			if ($query !== '') {
				$db->execute($query);
			}
		}
	}

}