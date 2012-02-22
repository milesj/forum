<?php
/**
 * AutoLoginComponent
 *
 * A CakePHP Component that will automatically login the Auth session for a duration if the user requested to (saves data to cookies).
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2011, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/code/cakephp/auto-login
 * 
 * @modified 	Mark Scherer - 2012-01-08 ms
 */

App::uses('Component', 'Controller');

class AutoLoginComponent extends Component {

	/**
	 * Current version.
	 *
	 * @access public
	 * @var string
	 */
	public $version = '3.4';

	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('Auth', 'Cookie');

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
	 * @access protected
	 * @var boolean
	 */
	protected $_debug = false;

	/**
	 * Default settings.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_defaults = array(
		// Model
		'model' => 'User',
		'username' => 'username',
		'password' => 'password',
		// Controller
		'plugin' => '',
		'controller' => 'users',
		'loginAction' => 'login',
		'logoutAction' => 'logout',
		// Cookie
		'cookieName' => 'autoLogin',
		'expires' => '+2 weeks', 		// Cookie length (strtotime() format)
		// Logic
		'redirect' => true,				// Force a redirect after successful autologin
		'requirePrompt' => true, 		// Displayed checkbox determines if cookie is created
		'active' => true				// Force the process to continue or exit
	);

	/**
	 * Determines whether to trigger startup() logic.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_isValidRequest = false;

	/**
	 * Detect debug info.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function initialize(Controller $controller) {
		$autoLogin = (array) Configure::read('AutoLogin');
		$this->settings = array_merge($this->_defaults, $autoLogin);

		// Backwards support
		if (isset($this->cookieName)) {
			$this->settings['cookieName'] = $this->cookieName;
		}

		if (isset($this->expires)) {
			$this->settings['expires'] = $this->expires;
		}

		// Validate the cookie
		$cookie = $this->Cookie->read($this->settings['cookieName']);
		$user = $this->Auth->user();
	
		// Is debug enabled
		$this->_debug = (!empty($autoLogin['email']) && !empty($autoLogin['ips']) && in_array(env('REMOTE_ADDR'), (array) $autoLogin['ips']));

		if (!$this->settings['active'] || !empty($user) || !$controller->request->is('get')) {
			return;

		} else if (!is_array($cookie) || !$cookie) {
			$this->debug('cookieFail', $this->Cookie, $user);
			$this->delete();
			return;

		} else if ($cookie['hash'] != $this->Auth->password($cookie['username'] . $cookie['time'])) {
			$this->debug('hashFail', $this->Cookie, $user);
			$this->delete();
			return;
		}

		// Set the data to identify with
		$controller->request->data[$this->settings['model']][$this->settings['username']] = $cookie['username'];
		$controller->request->data[$this->settings['model']][$this->settings['password']] = base64_decode($cookie['password']);

		// Request is valid, stop startup()
		$this->_isValidRequest = true;
	}

	/**
	 * Automatically login existent Auth session; called after controllers beforeFilter() so that Auth is initialized.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return boolean
	 */
	public function startup(Controller $controller) {
		if (!$this->_isValidRequest) {
			return;
		}

		if ($this->Auth->login()) {
			$this->debug('login', $this->Cookie, $this->Auth->user());

			if (in_array('_autoLogin', get_class_methods($controller))) {
				call_user_func_array(array($controller, '_autoLogin'), array(
					$this->Auth->user()
				));
			}

			if ($this->settings['redirect']) {
				$controller->redirect(array(), 301);
			}
			
		} else {
			$this->debug('loginFail', $this->Cookie, $this->Auth->user());

			if (in_array('_autoLoginError', get_class_methods($controller))) {
				call_user_func_array(array($controller, '_autoLoginError'), array(
					$this->Cookie->read($this->settings['cookieName'])
				));
			}
		}
	}

	/**
	 * Automatically process logic when hitting login/logout actions.
	 *
	 * @access public
	 * @uses Inflector
	 * @param Controller $controller
	 * @return void
	 */
	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
		if (empty($this->settings['active'])) {
			return;
		}

		$model = $this->settings['model'];

		if (is_array($this->Auth->loginAction)) {
			if (!empty($this->Auth->loginAction['controller'])) {
				$this->settings['controller'] = $this->Auth->loginAction['controller'];
			}

			if (!empty($this->Auth->loginAction['action'])) {
				$this->settings['loginAction'] = $this->Auth->loginAction['action'];
			}

			if (!empty($this->Auth->loginAction['plugin'])) {
				$this->settings['plugin'] = $this->Auth->loginAction['plugin'];
			}
		}

		if (empty($this->settings['controller'])) {
			$this->settings['controller'] = Inflector::pluralize($model);
		}

		// Is called after user login/logout validates, but before auth redirects
		if ($controller->plugin == Inflector::camelize($this->settings['plugin']) && $controller->name == Inflector::camelize($this->settings['controller'])) {
			$data = $controller->request->data;
			$action = isset($controller->request->params['action']) ? $controller->request->params['action'] : 'login';

			switch ($action) {
				case $this->settings['loginAction']:
					if (isset($data[$model])) {
						$username = $data[$model][$this->settings['username']];
						$password = $data[$model][$this->settings['password']];
						$autoLogin = isset($data[$model]['auto_login']) ? $data[$model]['auto_login'] : !$this->settings['requirePrompt'];

						if (!empty($username) && !empty($password) && $autoLogin) {
							$this->save($username, $password);

						} else if (!$autoLogin) {
							$this->delete();
						}
					}
				break;

				case $this->settings['logoutAction']:
					$this->debug('logout', $this->Cookie, $this->Auth->user());
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
		$cookie['username'] = $username;
		$cookie['password'] = base64_encode($password);
		$cookie['hash'] = $this->Auth->password($username . $time);
		$cookie['time'] = $time;

		if (env('REMOTE_ADDR') == '127.0.0.1' || env('HTTP_HOST') == 'localhost') {
			$this->Cookie->domain = false;
		}

		$this->Cookie->write($this->settings['cookieName'], $cookie, true, $this->settings['expires']);
		$this->debug('cookieSet', $cookie, $this->Auth->user());
	}

	/**
	 * Delete the cookie.
	 *
	 * @access public
	 * @return void
	 */
	public function delete() {
		$this->Cookie->delete($this->settings['cookieName']);
	}

	/**
	 * Debug the current auth and cookies.
	 *
	 * @access public
	 * @param string $key
	 * @param array $cookie
	 * @param array $user
	 * @return void
	 */
	public function debug($key, $cookie = array(), $user = array()) {
		$scopes = array(
			'login'				=> 'Login Successful',
			'loginFail'			=> 'Login Failure',
			'loginCallback'		=> 'Login Callback',
			'logout'			=> 'Logout',
			'logoutCallback'	=> 'Logout Callback',
			'cookieSet'			=> 'Cookie Set',
			'cookieFail'		=> 'Cookie Mismatch',
			'hashFail'			=> 'Hash Mismatch',
			'custom'			=> 'Custom Callback'
		);

		if ($this->_debug && isset($scopes[$key])) {
			$debug = (array) Configure::read('AutoLogin');
			$content = "";

			if (!empty($cookie) || !empty($user)) {
				if (!empty($cookie)) {
					$content .= "Cookie information: \n\n" . print_r($cookie, true) . "\n\n\n";
				}

				if (!empty($user)) {
					$content .= "User information: \n\n" . print_r($user, true);
				}
			} else {
				$content = 'No debug information.';
			}

			if (empty($debug['scope']) || in_array($key, (array) $debug['scope'])) {
				if (!empty($debug['email'])) {
					mail($debug['email'], '[AutoLogin] ' . $scopes[$key], $content, 'From: ' . $debug['email']);
				} else {
					$this->log($scopes[$key] . ': ' . $content, LOG_DEBUG);
				}
			}
		}
	}

}