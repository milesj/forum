<?php
/**
 * AutoLoginComponent
 *
 * A CakePHP Component that will automatically login the Auth session for a duration if the user requested to (saves data to cookies).
 *
 * @version		3.5.1
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
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('Auth', 'Cookie');

	/**
	 * Name of the user model.
	 *
	 * @access public
	 * @var string
	 */
	public $model = 'User';

	/**
	 * Field name for login username.
	 *
	 * @access public
	 * @var string
	 */
	public $username = 'username';

	/**
	 * Field name for login password.
	 *
	 * @access public
	 * @var string
	 */
	public $password = 'password';

	/**
	 * Plugin name if component is placed within a plugin.
	 *
	 * @access public
	 * @var string
	 */
	public $plugin = '';

	/**
	 * Users login/logout controller.
	 *
	 * @access public
	 * @var string
	 */
	public $controller = 'users';

	/**
	 * Users login action.
	 *
	 * @access public
	 * @var string
	 */
	public $loginAction = 'login';

	/**
	 * Users logout controller.
	 *
	 * @access public
	 * @var string
	 */
	public $logoutAction = 'logout';

	/**
	 * Name of the auto login cookie.
	 *
	 * @access public
	 * @var string
	 */
	public $cookieName = 'autoLogin';

	/**
	 * Duration in cookie length, using strtotime() format.
	 *
	 * @access public
	 * @var string
	 */
	public $expires = '+2 weeks';

	/**
	 * Force a redirect after successful auto login.
	 *
	 * @access public
	 * @var boolean
	 */
	public $redirect = true;

	/**
	 * Displayed checkbox determines if cookie is created.
	 *
	 * @access public
	 * @var boolean
	 */
	public $requirePrompt = true;

	/**
	 * Force the process to continue or exit.
	 *
	 * @access public
	 * @var boolean
	 */
	public $active = true;
	
	/**
	 * Should we debug?
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_debug = false;

	/**
	 * Determines whether to trigger startup() logic.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_isValidRequest = false;

	/**
	 * Initialize settings and debug.
	 *
	 * @access public
	 * @param ComponentCollection $collection
	 * @param array $settings
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$autoLogin = (array) Configure::read('AutoLogin');

		// Is debug enabled
		$this->_debug = (!empty($autoLogin['email']) && !empty($autoLogin['ips']) && in_array(env('REMOTE_ADDR'), (array) $autoLogin['ips']));

		parent::__construct($collection, array_merge((array) $settings, $autoLogin));
	}

	/**
	 * Detect cookie and hash and test for successful login persistence.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function initialize(Controller $controller) {
		$cookie = $this->read();
		$user = $this->Auth->user();

		if (!$this->active || !empty($user) || !$controller->request->is('get')) {
			return;

		} else if (!$cookie) {
			$this->debug('cookieFail', $this->Cookie, $user);
			$this->delete();
			return;

		} else if (empty($cookie['hash']) || $cookie['hash'] != $this->Auth->password($cookie['username'] . $cookie['time'])) {
			$this->debug('hashFail', $this->Cookie, $user);
			$this->delete();
			return;
		}

		// Set the data to identify with
		$controller->request->data[$this->model][$this->username] = $cookie['username'];
		$controller->request->data[$this->model][$this->password] = $cookie['password'];

		// Request is valid, stop startup()
		$this->_isValidRequest = true;
	}

	/**
	 * Automatically login existent Auth session; called after controllers beforeFilter() so that Auth is initialized.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function startup(Controller $controller) {
		if (!$this->_isValidRequest) {
			return;
		}

		// Backwards support
		if (isset($this->settings)) {
			$this->_set($this->settings);
		}

		if ($this->Auth->login()) {
			$this->debug('login', $this->Cookie, $this->Auth->user());

			if (in_array('_autoLogin', get_class_methods($controller))) {
				call_user_func_array(array($controller, '_autoLogin'), array(
					$this->Auth->user()
				));
			}

			if ($this->redirect) {
				$controller->redirect(array(), 301);
			}
			
		} else {
			$this->debug('loginFail', $this->Cookie, $this->Auth->user());

			if (in_array('_autoLoginError', get_class_methods($controller))) {
				call_user_func_array(array($controller, '_autoLoginError'), array(
					$this->read()
				));
			}
		}
	}

	/**
	 * Automatically process logic when hitting login/logout actions.
	 *
	 * @access public
	 * @param Controller $controller
	 * @param string $url
	 * @param int $status
	 * @param boolean $exit
	 * @return void
	 */
	public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
		if (!$this->active) {
			return;
		}

		$model = $this->model;

		if (is_array($this->Auth->loginAction)) {
			if (!empty($this->Auth->loginAction['controller'])) {
				$this->controller = $this->Auth->loginAction['controller'];
			}

			if (!empty($this->Auth->loginAction['action'])) {
				$this->loginAction = $this->Auth->loginAction['action'];
			}

			if (!empty($this->Auth->loginAction['plugin'])) {
				$this->plugin = $this->Auth->loginAction['plugin'];
			}
		}

		if (empty($this->controller)) {
			$this->controller = Inflector::pluralize($model);
		}

		// Is called after user login/logout validates, but before auth redirects
		if ($controller->plugin == Inflector::camelize($this->plugin) && $controller->name == Inflector::camelize($this->controller)) {
			$data = $controller->request->data;
			$action = isset($controller->request->params['action']) ? $controller->request->params['action'] : 'login';

			switch ($action) {
				case $this->loginAction:
					if (isset($data[$model])) {
						$username = $data[$model][$this->username];
						$password = $data[$model][$this->password];
						$autoLogin = isset($data[$model]['auto_login']) ? $data[$model]['auto_login'] : !$this->requirePrompt;

						if (!empty($username) && !empty($password) && $autoLogin) {
							$this->save($username, $password);

						} else if (!$autoLogin) {
							$this->delete();
						}
					}
				break;

				case $this->logoutAction:
					$this->debug('logout', $this->Cookie, $this->Auth->user());
					$this->delete();
				break;
			}
		}
	}

	/**
	 * Read the AutoLogin cookie and base64_decode().
	 *
	 * @access public
	 * @return array|boolean
	 */
	public function read() {
		$cookie = $this->Cookie->read($this->cookieName);

		if (empty($cookie) || !is_array($cookie)) {
			return false;
		}

		if (isset($cookie['username'])) {
			$cookie['username'] = base64_decode($cookie['username']);
		}

		if (isset($cookie['password'])) {
			$cookie['password'] = base64_decode($cookie['password']);
		}

		return $cookie;
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
		$cookie['username'] = base64_encode($username);
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