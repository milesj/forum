<?php
/** 
 * Forum Plugin AppController
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
  
Configure::load('Forum.config');

class ForumAppController extends AppController {

	/**
	 * Remove parent models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array();

	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('RequestHandler', 'Session', 'Security', 'Cookie', 'Auth', 'Forum.Toolbar', 'Forum.AutoLogin');
	
	/**
	 * Helpers.
	 *
	 * @access public
	 * @var array
	 */
	public $helpers = array('Html', 'Session', 'Form', 'Time', 'Text', 'Forum.Common', 'Forum.Decoda' => array());

	/**
	 * Run auto login logic.
	 *
	 * @access public
	 * @param array $user - The logged in User
	 * @return void
	 */
	public function _autoLogin($user) {
		ClassRegistry::init('Forum.User')->login($user);

		$this->Session->delete('Forum');
		$this->Toolbar->initForum();
	}

	/**
	 * Refreshes the Auth to get new data.
	 *
	 * @access public
	 * @param string $field
	 * @param string $value
	 * @return void
	 */
	public function _refreshAuth($field = '', $value = '') {
		if (!empty($field) && !empty($value)) {
			$this->Session->write($this->Auth->sessionKey .'.'. $field, $value);
		} else {
			if (isset($this->User)) {
				$this->Auth->login($this->User->read(false, $this->Auth->user('id')));
			} else {
				$this->Auth->login(ClassRegistry::init('Forum.User')->findById($this->Auth->user('id')));
			}
		}
	}
	
	/**
	 * Before filter.
	 * 
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Settings
		Configure::write('Forum.settings', ClassRegistry::init('Forum.Setting')->getSettings());
		
		// Localization
		$locale = $this->Auth->user('locale') ? $this->Auth->user('locale') : Configure::read('Forum.settings.default_locale');
		Configure::write('Config.language', $locale);
		setlocale(LC_ALL, $locale .'UTF8', $locale .'UTF-8', $locale, 'eng.UTF8', 'eng.UTF-8', 'eng', 'en_US');
		
		// Authorization
		$referer = $this->referer();
		$routes = Configure::read('Forum.routes');

		if (empty($referer) || $referer == '/forum/users/login' || $referer == '/admin/forum/users/login') {
			$referer = array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index');
		}

		$this->Auth->loginAction = Configure::read('Forum.routes.login');
		$this->Auth->loginRedirect = $referer;
		$this->Auth->logoutRedirect = $referer;
		$this->Auth->autoRedirect = false;
		
		// AutoLogin
		$this->AutoLogin->settings = array(
			'plugin' => $routes['login']['plugin'],
			'controller' => $routes['login']['controller'],
			'loginAction' => $routes['login']['action'],
			'logoutAction' => $routes['logout']['action']
		);

		// Helpers
		if ($censored = Configure::read('Forum.settings.censored_words')) {
			$this->helpers['Forum.Decoda'] = array('censored' => explode(',', str_replace(', ', ',', $censored)));
		}
		
		// Initialize
		$this->Toolbar->initForum();
	}

	/**
	 * Before render.
	 *
	 * @access public
	 * @return void
	 */
	public function beforeRender() {
		$this->set('plugin', Configure::read('Forum'));
		$this->set('settings', Configure::read('Forum.settings'));
	}

}
 