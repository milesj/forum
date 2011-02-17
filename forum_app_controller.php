<?php
/** 
 * Cupcake - Forum Plugin AppController
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/forum-plugin
 */
  
App::import(array(
	'type' => 'File', 
	'name' => 'Forum.ForumConfig', 
	'file' => 'config'. DS .'core.php'
));

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
	public $components = array('RequestHandler', 'Session', 'Security', 'Cookie', 'Forum.Toolbar');
	
	/**
	 * Helpers.
	 *
	 * @access public
	 * @var array
	 */
	public $helpers = array('Html', 'Session', 'Form', 'Time', 'Text', 'Forum.Cupcake', 'Forum.Decoda' => array());

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
		
		$Config = ForumConfig::getInstance();

		// Load l10n/i18n support
		if (isset($this->Auth) && $this->Auth->user('locale')) {
			$locale = $this->Auth->user('locale');
		} else {
			$locale = (isset($Config->settings['default_locale']) ? $Config->settings['default_locale'] : 'eng');
		}

		Configure::write('Config.language', $locale);
		setlocale(LC_ALL, $locale .'UTF8', $locale .'UTF-8', $locale, 'eng.UTF8', 'eng.UTF-8', 'eng', 'en_US');
		
		// Auth settings
		$referer = $this->referer();
		if (empty($referer) || $referer == '/forum/users/login' || $referer == '/admin/forum/users/login') {
			$referer = array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index');
		}

		if (isset($this->Auth)) {
			$this->Auth->loginAction = array('plugin' => 'forum', 'controller' => 'users', 'action' => 'login', 'admin' => false);
			$this->Auth->loginRedirect = $referer;
			$this->Auth->logoutRedirect = $referer;
			$this->Auth->autoRedirect = false;
			$this->Auth->userModel = 'Forum.User';

			// AutoLogin settings
			$this->AutoLogin->settings = array(
				'plugin' => 'forum',
				'controller' => 'users',
				'loginAction' => 'login',
				'logoutAction' => 'logout'
			);
		}

		$this->Cookie->key = Configure::read('Security.salt');
		
		// Apply censored words
		if (!empty($Config->settings['censored_words'])) {
			$censored = explode(',', str_replace(', ', ',', $Config->settings['censored_words']));
			$this->helpers['Forum.Decoda'] = array('censored' => $censored);
		}
		
		// Initialize
		$this->Toolbar->initForum();
	}

	/**
	 * Check page title and set for 1.3.
	 */
	public function beforeRender() {
		if (isset($this->pageTitle) && !empty($this->pageTitle)) {
			$this->set('title_for_layout', $this->pageTitle);
		}
	}

}
 