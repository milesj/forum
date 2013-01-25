<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

Configure::write('debug', 2);
Configure::write('Cache.disable', true);

App::uses('ConnectionManager', 'Model');

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
		'2.2' => 'Subscriptions'
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

			if ($this->_querySql($version)) {
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