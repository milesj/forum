<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('BaseInstallShell', 'Utility.Console/Command');

class InstallShell extends BaseInstallShell {

	/**
	 * Trigger install.
	 */
	public function main() {
		if (!CakePlugin::loaded('Admin')) {
			$this->err('Admin plugin is not installed, aborting!');
			return;
		}

		$this->setSteps(array(
			'Check Database Configuration' => 'checkDbConfig',
			'Set Table Prefix' => 'checkTablePrefix',
			'Set Users Table' => 'checkUsersTable',
			'Check Table Status' => 'checkRequiredTables',
			'Create Database Tables' => 'createTables',
			'Finish Installation' => 'finish'
		))
		->setDbConfig(FORUM_DATABASE)
		->setTablePrefix(FORUM_PREFIX)
		->setRequiredTables(array('aros', 'acos', 'aros_acos'));

		$this->out('Plugin: Forum v' . Configure::read('Forum.version'));
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');

		parent::main();
	}

	/**
	 * Finalize the installation.
	 *
	 * @return bool
	 */
	public function finish() {
		$this->hr(1);
		$this->out('Forum installation complete!');
		$this->out('Please read the documentation for further instructions:');
		$this->out('http://milesj.me/code/cakephp/forum');
		$this->hr(1);

		return true;
	}

}