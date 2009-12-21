<?php
/**
 * auto_login.php
 *
 * A CakePHP Component that will automatically login the Auth session for a duration if the user requested to (saves data to cookies).
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		AutoLogin Component
 * @version 	1.6
 * @link		www.milesj.me/resources/script/auto-login-component
 */

class AutoLoginComponent extends Object {

	/**
	 * Current version: www.milesj.me/files/logs/auto-login-component
	 * @access public
	 * @var string
	 */
	public $version = '1.6';

	/**
	 * Cookie name
	 * @access public
	 * @var string
	 */
	public $cookieName = 'autoLogin';

	/**
	 * Cookie length (strtotime())
	 * @access public
	 * @var string
	 */
	public $expires = '+2 weeks';

	/**
	 * Settings
	 * @access public
	 * @var array
	 */
	public $settings = array(
		'plugin' => '',
		'controller' => '',
		'loginAction' => '',
		'logoutAction' => ''
	);

	/**
	 * Attemps tp grab the controllers cookie class
	 * @access public
	 * @param object $Controller
	 * @return void
	 */
	public function initialize(&$Controller) {
		if (isset($Controller->Cookie)) {
			$this->Cookie = $Controller->Cookie;
		} else {
			App::import('Component', 'Cookie');
			$this->Cookie = new CookieComponent();
		}
	}

	/**
	 * Automatically login existent Auth session; called after controllers beforeFilter() so that Auth is initialized
	 * @access public
	 * @param object $Controller
	 * @return boolean
	 */
	public function startup(&$Controller) {
		if (isset($Controller->Cookie)) {
			$this->Cookie = $Controller->Cookie;
		}
		$cookie = $this->Cookie->read($this->cookieName);

		if (!is_array($cookie) || $Controller->Auth->user()) {
			return;
		}

		if ($cookie['hash'] != $Controller->Auth->password($cookie[$Controller->Auth->fields['username']] . $cookie['time'])) {
			$this->delete();
			return;
		}

		if ($Controller->Auth->login($cookie)) {
			if (in_array('_autoLogin', get_class_methods($Controller))) {
				call_user_func_array(array(&$Controller, '_autoLogin'), array($Controller->Auth->user()));
			}
		} else {
			if (in_array('_autoLoginError', get_class_methods($Controller))) {
				call_user_func_array(array(&$Controller, '_autoLoginError'), array($cookie));
			}
		}

		return true;
	}

	/**
	 * Automatically process logic when hitting login/logout actions
	 * @access public
	 * @uses Inflector
	 * @param object $Controller
	 * @return void
	 */
	public function beforeRedirect(&$Controller) {
		$plugin 	= $this->settings['plugin'];
		$controller = $this->settings['controller'];
		$loginAction 	= $this->settings['loginAction'];
		$logoutAction 	= $this->settings['logoutAction'];

		if (is_array($Controller->Auth->loginAction)) {
			if (!empty($Controller->Auth->loginAction['controller'])) {
				$controller = Inflector::camelize($Controller->Auth->loginAction['controller']);
			}

			if (!empty($Controller->Auth->loginAction['action'])) {
				$loginAction = $Controller->Auth->loginAction['action'];
			}
		}

		if (!empty($Controller->Auth->userModel) && empty($controller)) {
			$controller = Inflector::pluralize($Controller->Auth->userModel);
		}

		if (empty($loginAction)) {
			$loginAction = 'login';
		}

		if (empty($logoutAction)) {
			$logoutAction = 'logout';
		}

		// Is called after user login/logout validates, but befire auth redirects
		if ($Controller->plugin == $plugin && $Controller->name == $controller) {
			$data = $Controller->data;

			switch ($Controller->action) {
				case $loginAction:
					$username = $data[$Controller->Auth->userModel][$Controller->Auth->fields['username']];
					$password = $data[$Controller->Auth->userModel][$Controller->Auth->fields['password']];
					$autoLogin = (isset($data[$Controller->Auth->userModel]['auto_login'])) ? $data[$Controller->Auth->userModel]['auto_login'] : 0;

					if (!empty($username) && !empty($password) && $autoLogin == 1) {
						$this->save($username, $password, $Controller);
					} else if ($autoLogin == 0) {
						$this->delete();
					}
				break;

				case $logoutAction:
					$this->delete();
				break;
			}
		}
	}

	/**
	 * Remember the user information
	 * @access public
	 * @param string $username
	 * @param string $password
	 * @param object $Controller
	 * @return void
	 */
	public function save($username, $password, $Controller) {
		$time = time();
		$cookie = array();
		$cookie[$Controller->Auth->fields['username']] = $username;
		$cookie[$Controller->Auth->fields['password']] = $password; // Already hashed from auth
		$cookie['hash'] = $Controller->Auth->password($username . $time);
		$cookie['time'] = $time;

		$this->Cookie->write($this->cookieName, $cookie, true, $this->expires);
	}

	/**
	 * Delete the cookie
	 * @access public
	 * @return void
	 */
	public function delete() {
		$this->Cookie->del($this->cookieName);
	}

}
