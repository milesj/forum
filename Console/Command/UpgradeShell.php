<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('BaseUpgradeShell', 'Utility.Console/Command');

class UpgradeShell extends BaseUpgradeShell {

	/**
	 * Trigger upgrade.
	 */
	public function main() {
		if (!CakePlugin::loaded('Admin')) {
			$this->err('Admin plugin is not installed, aborting!');
			return;
		}

		$fieldMap = Configure::read('User.fieldMap');

		$this->setSteps(array(
			'Check Database Configuration' => 'checkDbConfig',
			'Set Table Prefix' => 'checkTablePrefix',
			'Set Users Table' => 'checkUsersTable',
			'Upgrade Version' => 'versions'
		))
		->setVersions(array(
			'4.0.0' => 'Admin + Utility Plugin Migration'
		))
		->setDbConfig(FORUM_DATABASE)
		->setTablePrefix(FORUM_PREFIX)
		->setUserFields(array(
			'username' => $fieldMap['username'],
			'password' => $fieldMap['password'],
			'email' => $fieldMap['email']
		));

		$this->out('Plugin: Forum v' . Configure::read('Forum.version'));
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');

		parent::main();
	}

	/**
	 * Upgrade to 4.0.0.
	 */
	public function to_400() {
		$answer = strtoupper($this->in('This upgrade will delete the following tables after migration: settings, access, access_levels, profiles, reported. All data will be migrated to the new admin system, are you sure you want to continue?', array('Y', 'N')));

		if ($answer === 'N') {
			exit();
		}

		// Migrate old reports to the new admin system
		$this->out('Migrating reports...');

		$ItemReport = ClassRegistry::init('Admin.ItemReport');

		$Reported = new AppModel(null, 'reported', $this->dbConfig);
		$Reported->alias = 'Reported';
		$Reported->tablePrefix = $this->tablePrefix;

		foreach ($Reported->find('all') as $report) {
			switch ($report['Report']['itemType']) {
				case 1: $model = 'Forum.Topic'; break;
				case 2: $model = 'Forum.Post'; break;
				case 3: $model = $this->usersModel; break;
			}

			$ItemReport->reportItem(array(
				'reporter_id' => $report['Report']['user_id'],
				'model' => $model,
				'foreign_key' => $report['Report']['item_id'],
				'item' => $report['Report']['item_id'],
				'reason' => $report['Report']['comment'],
				'created' => $report['Report']['created']
			));
		}

		// Migrate profile data to users table
		$this->out('Migrating user profiles...');

		$User = ClassRegistry::init($this->usersModel);
		$fieldMap = Configure::read('User.fieldMap');

		$Profile = new AppModel(null, 'profiles', $this->dbConfig);
		$Profile->alias = 'Profile';
		$Profile->tablePrefix = $this->tablePrefix;

		foreach ($Profile->find('all') as $prof) {
			$query = array();

			foreach (array('signature', 'locale', 'timezone', 'totalPosts', 'totalTopics', 'lastLogin') as $field) {
				if ($key = $fieldMap[$field]) {
					$query[$key] = $prof['Profile'][$field];
				}
			}

			if (!$query) {
				continue;
			}

			$User->id = $prof['Profile']['user_id'];
			$User->save($query, false);
		}

		// Delete tables handled by parent shell
		$this->out('Deleting old tables...');
	}

}