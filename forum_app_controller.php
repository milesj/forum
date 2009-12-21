<?php
/** 
 * forum_app_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Forum Plugin AppController
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum  
 */
  
App::import(array(
	'type' => 'File', 
	'name' => 'Forum.ForumConfig', 
	'file' => 'config'. DS .'core.php'
));
 
class ForumAppController extends AppController {

	/**
	 * Components
	 * @access public
	 * @var array
	 */
	public $components = array('RequestHandler', 'Security', 'Cookie', 'Auth', 'Forum.Toolbar', 'Forum.AutoLogin');
	
	/**
	 * Helpers
	 * @access public
	 * @var array
	 */
	public $helpers = array('Html', 'Session', 'Form', 'Time', 'Text', 'Javascript', 'Forum.Cupcake', 'Forum.Decoda' => array());
	
	/**
	 * Custom method to setup your settings, only edit this
	 * @access protected
	 * @return void
	 */
	public function _initForum() {
		Security::setHash('md5');
	}
	
	/**
	 * Initialize the session and all data
	 * @access protected
	 * @return void
	 */
	public function _initSession() {
		if (!$this->Session->check('Forum.isBrowsing')) {
			$user_id = $this->Auth->user('id');
			
			// How much access we have?
			if (!$this->Session->check('Forum.access')) {
				$access = array('Guest' => 0);
				
				if ($user_id) {
					$access['Member'] = 1;
					$access = array_merge($access, ClassRegistry::init('Forum.Access')->getMyAccess($user_id));
				}
				
				$this->Session->write('Forum.access', $access);
			}
			
			// Save last visit time
			if (!$this->Session->check('Forum.lastVisit')) {
				$lastVisit = ($user_id) ? $this->Auth->user('lastLogin') : date('Y-m-d H:i:s');
				$this->Session->write('Forum.lastVisit', $lastVisit);
			}
			
			// Moderator?
			if (!$this->Session->check('Forum.moderates')) {
				$moderates = ($user_id) ? ClassRegistry::init('Forum.Moderator')->getModerations($user_id) : array();
				$this->Session->write('Forum.moderates', $moderates);
			}
			
			// Are we a super mod?
			if (!$this->Session->check('Forum.isSuperMod')) {
				$status = ($user_id) ? ClassRegistry::init('Forum.Access')->isSuper($user_id) : 0;
				$this->Session->write('Forum.isSuperMod', $status);
			}
			
			// Are we an administrator?
			if (!$this->Session->check('Forum.isAdmin')) {
				$status = ($user_id) ? ClassRegistry::init('Forum.Access')->isAdmin($user_id) : 0;
				$this->Session->write('Forum.isAdmin', $status);
			}
			
			$this->Session->write('Forum.isBrowsing', true);
		}
	}
	
	/**
	 * Run auto login logic
	 * @access protected
	 * @param array $user - The logged in User
	 * @return void
	 */
	public function _autoLogin($user) {
		$this->Session->delete('Forum');
		ClassRegistry::init('User')->login($user);
		
		$this->_initForum();
		$this->_initSession();
	}

	/**
	 * Refreshes the Auth to get new data
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
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->_initForum();

		// Load l10n/i18n support
		if ($locale = $this->Auth->user('locale')) {
			Configure::write('Config.language', $locale);
			setlocale(LC_ALL, $locale .'UTF8', $locale .'UTF-8', $locale, 'eng.UTF8', 'eng.UTF-8', 'eng', 'en_US');
		}
		
		// Auth settings
		$referer = $this->referer();
		if (empty($referer) || $referer == '/forum/users/login' || $referer == '/admin/forum/users/login') {
			$referer = array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index');
		}
		
		$this->Auth->loginAction = array('plugin' => 'forum', 'controller' => 'users', 'action' => 'login', 'admin' => false);
		$this->Auth->loginRedirect = $referer;
		$this->Auth->logoutRedirect = $referer;
		$this->Auth->autoRedirect = false;
		
		// AutoLogin settings
		$this->AutoLogin->settings = array(
			'plugin' => 'forum',
			'controller' => 'users',
			'loginAction' => 'login',
			'logoutAction' => 'logout'
		);

		$this->Cookie->key = 'cupcake';
		
		// Apply censored words
		$Config = ForumConfig::getInstance();
		
		if (!empty($Config->settings['censored_words'])) {
			$censored = explode(',', str_replace(', ', ',', $Config->settings['censored_words']));
			$this->helpers['Forum.Decoda'] = array('censored' => $censored);
		}
		
		// Initialize
		$this->_initSession();
	}

}
 