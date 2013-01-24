<?php
/**
 * ForumAppController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

Configure::write('Forum.settings', ClassRegistry::init('Forum.Setting')->getSettings());

class ForumAppController extends AppController {

	/**
	 * Remove parent models.
	 *
	 * @var array
	 */
	public $uses = array();

	/**
	 * Components.
	 *
	 * @var array
	 */
	public $components = array('Session', 'Security', 'Cookie', 'Auth', 'Forum.ForumToolbar', 'Utility.AutoLogin');

	/**
	 * Helpers.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html', 'Session', 'Form', 'Time', 'Text',
		'Utility.Breadcrumb', 'Utility.OpenGraph', 'Utility.Decoda',
		'Forum.Forum'
	);

	/**
	 * Plugin configuration.
	 *
	 * @var array
	 */
	public $config = array();

	/**
	 * Database forum settings.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Run auto login logic.
	 *
	 * @param array $user
	 * @return void
	 */
	public function _autoLogin($user) {
		ClassRegistry::init('Forum.Profile')->login($user['User']['id']);

		$this->Session->delete('Forum');
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->set('menuTab', '');

		// Settings
		$this->config = Configure::read('Forum');
		$this->settings = Configure::read('Forum.settings');

		// Admin
		if (isset($this->request->params['admin'])) {
			$this->ForumToolbar->verifyAdmin();
			$this->layout = 'admin';
		} else {
			$this->layout = $this->config['viewLayout'];
		}

		// Localization
		$locale = $this->Auth->user('locale') ?: $this->settings['default_locale'];
		Configure::write('Config.language', $locale);
		setlocale(LC_ALL, $locale . 'UTF8', $locale . 'UTF-8', $locale, 'eng.UTF8', 'eng.UTF-8', 'eng', 'en_US');

		// Authorization
		$referrer = $this->referer();
		$routes = $this->config['routes'];

		if (!$referrer || strpos($referrer, 'users/login') !== false) {
			$referrer = array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index');
		}

		$this->Auth->loginAction = $routes['login'];
		$this->Auth->loginRedirect = $referrer;
		$this->Auth->logoutRedirect = $referrer;

		// AutoLogin
		$this->AutoLogin->settings = array(
			'model' => FORUM_USER,
			'username' => $this->config['userMap']['username'],
			'password' => $this->config['userMap']['password'],
			'plugin' => $routes['login']['plugin'],
			'controller' => $routes['login']['controller'],
			'loginAction' => $routes['login']['action'],
			'logoutAction' => $routes['logout']['action']
		);
	}

	/**
	 * Before render.
	 */
	public function beforeRender() {
		$user = $this->Auth->user();

		if ($user) {
			$user = array('User' => $user);
		}

		$this->set('user', $user);
		$this->set('config', $this->config);
		$this->set('settings', $this->settings);
	}

}
