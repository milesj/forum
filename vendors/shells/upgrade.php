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
Configure::load('Forum.config');

App::import('Core', array('File', 'Security', 'Sanitize', 'Validation'));
App::import('Model', 'ConnectionManager', false);

define('FORUM_PLUGIN', dirname(dirname(dirname(__FILE__))) . DS);
define('FORUM_SCHEMA', FORUM_PLUGIN .'config'. DS .'schema'. DS);

include_once CONFIGS . 'database.php';

class UpgradeShell extends Shell {
	
	/**
	 * Plugin configuration.
	 * 
	 * @access public
	 * @var array
	 */
	public $config = array();
	
	/**
	 * The current version.
	 * 
	 * @access public
	 * @var string
	 */
	public $version = '0.0';
	
	/**
	 * Execute upgrader!
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
		$this->out('Shell: Upgrade');
		$this->out();
		$this->out('This shell will upgrade versions and manage any database changes.');
		$this->out('Please do not skip versions, upgrade sequentially!');	
		
		$this->upgrade();
		$this->finalize();
	}
	
	/**
	 * List out all the available upgrade options.
	 */
	public function upgrade() {	
		$this->hr(1);
		$this->out('Available versions:');
		$this->out();
		$this->out('[2.2] Subscriptions');
		
		$answer = $this->in('Which version do you want to upgrade to?', array('2.2'));
		
		switch ($answer) {
			case '2.2':
				$this->version_2_2();
			break;
			default:
				$this->out('Please select one of the following versions: 2.2');
				$this->upgrade();
			break;
		}
	}
	
	/**
	 * Output complete message and render versions again.
	 */
	public function finalize() {
		$this->out('You can now upgrade to another version or close the shell.');
		$this->hr(1);
		$this->upgrade();
	}
	
	/**
	 * Install the subscription system introduced in 2.2.
	 */
	public function version_2_2() {
		$this->hr(1);
		$this->out('Upgrading to 2.2...');
		
		// do something
		
		$this->out('Upgrade to 2.2 is complete.');
	}
	
}