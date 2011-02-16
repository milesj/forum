<?php
/**
* Auto Login Component
*
* A CakePHP Component that will automatically login the Auth session for a duration if the user requested to (saves data to cookies).
*
* @author       Miles Johnson - http://milesj.me
* @copyright    Copyright 2006-2010, Miles Johnson, Inc.
* @license      http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
* @link         http://milesj.me/resources/script/auto-login-component
*/

class AutoLoginComponent extends Object {

	/**
	 * Current version: http://milesj.me/resources/logs/auto-login-component
	 *
	 * @access public
	 * @var string
	 */
	public $version = '2.0';

	/**
	 * Cookie name.
	 *
	 * @access public
	 * @var string
	 */
	public $cookieName = 'autoLogin';

	/**
	 * Cookie length (strtotime()).
	 *
	 * @access public
	 * @var string
	 */
	public $expires = '+2 weeks';

	/**
	 * Settings.
	 *
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * Should we debug?
	 *
	 * @access public
	 * @var boolean
	 */
	public $debug = false;

	/**
	 * Detect debug info.
	 *
	 * @access public
	 * @param object $Controller
	 * @return void
	 */
	public function initialize($Controller) {
		$debug = Configure::read('AutoLogin');

		if (!is_array($debug['ips'])) {
			$debug['ips'] = array($debug['ips']);
			Configure::write('AutoLogin.ips', $debug['ips']);
		}

		$this->debug = (isset($debug['email']) && isset($debug['ips']) && in_array(env('REMOTE_ADDR'), $debug['ips']));
	}

	/**
	 * Automatically login existent Auth session; called after controllers beforeFilter() so that Auth is initialized.
	 *
	 * @access public
	 * @param object $Controller
	 * @return boolean
	 */
	public function startup($Controller) {
		$this->Controller = $Controller;
		$this->Auth = $this->Controller->Auth;

		if (isset($this->Controller->Cookie)) {
			$this->Cookie = $this->Controller->Cookie;
		}

		if (!isset($this->Cookie)) {
			App::import('Component', 'Cookie');

			$this->Cookie = new CookieComponent();
			$this->Cookie->initialize($Controller, array());
		}

		$cookie = $this->Cookie->read($this->cookieName);
		$user = $this->Auth->user();

		if ($user) {
			return;

		} else if (!is_array($cookie)) {
			$this->debug('Cookie failed', $cookie, $user);
			$this->delete();
			return;

		} else if ($cookie['hash'] != $this->Auth->password($cookie[$this->Auth->fields['username']] . $cookie['time'])) {
			$this->debug('Hash failed', $cookie, $user);
			$this->delete();
			return;
		}

		if ($this->Auth->login($cookie)) {
			$user = $this->Auth->user();
			$this->debug('Login succesful', $cookie, $user);

			if (in_array('_autoLogin', get_class_methods($Controller))) {
				call_user_func_array(array($Controller, '_autoLogin'), array($user));
			}
		} else {
			$this->debug('Login failed', $cookie, $user);

			if (in_array('_autoLoginError', get_class_methods($Controller))) {
				call_user_func_array(array($Controller, '_autoLoginError'), array($cookie));
			}
		}

		return;
	}

	/**
	 * Automatically process logic when hitting login/logout actions.
	 *
	 * @access public
	 * @uses Inflector
	 * @param object $Controller
	 * @return void
	 */
	public function beforeRedirect($Controller) {
		$this->settings = $this->settings + array(
			'plugin' => '',
			'controller' => '',
			'loginAction' => 'login',
			'logoutAction' => 'logout'
		);

		if (is_array($this->Auth->loginAction)) {
			if (!empty($this->Auth->loginAction['controller'])) {
				$this->settings['controller'] = Inflector::camelize($this->Auth->loginAction['controller']);
			}

			if (!empty($this->Auth->loginAction['action'])) {
				$this->settings['loginAction'] = $this->Auth->loginAction['action'];
			}
		}

		if (!empty($this->Auth->userModel) && empty($this->settings['controller'])) {
			$this->settings['controller'] = Inflector::pluralize($this->Auth->userModel);
		}

		// Is called after user login/logout validates, but befire auth redirects
		if ($this->Controller->plugin == $this->settings['plugin'] && $this->Controller->name == $this->settings['controller']) {
			$data = $this->Controller->data;

			switch ($this->Controller->action) {
				case $this->settings['loginAction']:
					if (isset($data[$this->Auth->userModel])) {
						$formData = $data[$this->Auth->userModel];
						$username = $formData[$this->Auth->fields['username']];
						$password = $formData[$this->Auth->fields['password']];
						$autoLogin = isset($formData['auto_login']) ? $formData['auto_login'] : 0;

						if (!empty($username) && !empty($password) && $autoLogin == 1) {
							$this->save($username, $password);

						} else if ($autoLogin == 0) {
							$this->delete();
						}
					}
				break;

				case $this->settings['logoutAction']:
					$this->delete();
				break;
			}
		}
	}

	/**
	 * Remember the user information.
	 *
	 * @access public
	 * @param string $username
	 * @param string $password
	 * @return void
	 */
	public function save($username, $password) {
		$time = time();
		$cookie = array();
		$cookie[$this->Auth->fields['username']] = $username;
		$cookie[$this->Auth->fields['password']] = $password; // Already hashed from auth
		$cookie['hash'] = $this->Auth->password($username . $time);
		$cookie['time'] = $time;

		$this->Cookie->write($this->cookieName, $cookie, true, $this->expires);
		$this->debug('Cookie set', $cookie);
	}

	/**
	 * Delete the cookie.
	 *
	 * @access public
	 * @return void
	 */
	public function delete() {
		$this->Cookie->delete($this->cookieName);
	}

	/**
	 * Debug the current auth/cookies.
	 *
	 * @access public
	 * @param string $message
	 * @param array $cookie
	 * @param array $user
	 * @return void
	 */
	public function debug($message, $cookie = array(), $user = array()) {
		if ($this->debug) {
			$email = Configure::read('AutoLogin.email');
			$content = 'No debug information.';

			if (!empty($cookie)) {
				$content = "Cookie information: \n\n". print_r($cookie, true) ."\n\n\n";
			}

			if (!empty($user)) {
				$content .= "User information: \n\n". print_r($user, true);
			}

			mail($email, '[AutoLogin] '. $message, $content, 'From: '. $email);
		}
	}

}