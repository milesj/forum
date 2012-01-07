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
 */

class AutoLoginComponent extends Component {

	/**
	 * Current version.
	 *
	 * @access public
	 * @var string
	 */
	public $version = '3.2.1';

	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('Auth', 'Cookie');

	/**
	 * Cookie name.
	 *
	 * @access public
	 * @var string
	 */
	public $cookieName = 'autoLogin';

	/**
	 * Cookie length (strtotime() format).
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
		'model' => 'User',
		'username' => 'username',
		'password' => 'password',
		'plugin' => '',
		'controller' => 'users',
		'loginAction' => 'login',
		'logoutAction' => 'logout'
	);
	
	/**
	 * Detect debug info.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function initialize($controller) {
		$debug = Configure::read('AutoLogin');

		$this->_debug = (isset($debug['email']) && isset($debug['ips']) && in_array(env('REMOTE_ADDR'), (array) $debug['ips']));
	}

	/**
	 * Automatically login existent Auth session; called after controllers beforeFilter() so that Auth is initialized.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return boolean
	 */
	public function startup($controller) {
		$this->settings = $this->settings + $this->_defaults;
		
		$cookie = $this->Cookie->read($this->cookieName);
		$user = $this->Auth->user();

		if (!empty($user)) {
			return;

		} else if (!is_array($cookie)) {
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

		if ($this->Auth->login()) {
			$this->debug('login', $this->Cookie, $this->Auth->user());

			if (in_array('_autoLogin', get_class_methods($controller))) {
				call_user_func_array(array($controller, '_autoLogin'), array($this->Auth->user()));
			}
		} else {
			$this->debug('loginFail', $this->Cookie, $user);

			if (in_array('_autoLoginError', get_class_methods($controller))) {
				call_user_func_array(array($controller, '_autoLoginError'), array($cookie));
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
	public function beforeRedirect($controller) {
		$this->settings = $this->settings + $this->_defaults;
		
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
		if ($controller->plugin == $this->settings['plugin'] && $controller->name == Inflector::camelize($this->settings['controller'])) {
			$data = $controller->request->data;
			$action = isset($controller->request->params['action']) ? $controller->request->params['action'] : 'login';

			switch ($action) {
				case $this->settings['loginAction']:
					if (isset($data[$model])) {
						$username = $data[$model][$this->settings['username']];
						$password = $data[$model][$this->settings['password']];
						$autoLogin = isset($data[$model]['auto_login']) ? $data[$model]['auto_login'] : 0;

						if (!empty($username) && !empty($password) && $autoLogin) {
							$this->save($username, $password);

						} else if ($autoLogin == 0) {
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

		$this->Cookie->write($this->cookieName, $cookie, true, $this->expires);
		$this->debug('cookieSet', $cookie, $this->Auth->user());
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
			$debug = Configure::read('AutoLogin');
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

			if (empty($debug['scope']) || in_array($key, $debug['scope'])) {
				mail($debug['email'], '[AutoLogin] ' . $scopes[$key], $content, 'From: ' . $debug['email']);
			}
		}
	}

}