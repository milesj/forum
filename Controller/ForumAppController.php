<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

ClassRegistry::init('Forum.Setting')->configureSettings();

/**
 * @property ForumToolbarComponent $ForumToolbar
 */
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
	public $components = array(
		'Session', 'Security', 'Cookie', 'Acl',
		'Auth' => array(
			'authorize' => array('Controller')
		),
		'Utility.AutoLogin',
		'Forum.ForumToolbar'
	);

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
	 * Validate the user has the correct ACL permissions.
	 *
	 * @param array $user
	 * @return bool
	 */
	public function isAuthorized($user) {
		if (isset($this->request->params['admin'])) {
			return $this->Session->read('Forum.isAdmin');
		}

		// Admins can do everything
		if ($this->Session->read('Forum.isAdmin')) {
			return true;
		}

		$controller = strtolower($this->name);
		$action = $this->request->params['action'];

		// Change to polls when applicable
		if (isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] === 'poll') {
			$controller = 'polls';
		}

		// Allow for controllers that don't have ACL
		if (!in_array($controller, array('stations', 'topics', 'posts', 'polls'))) {
			return true;
		}

		// Validate based on action
		switch ($action) {

			// Allow if the user belongs to admin or super
			case 'moderate':
				return $this->Session->read('Forum.isSuper');
			break;

			// Check individual permissions
			case 'add':
			case 'view':
			case 'edit':
			case 'delete':
				$crud = array(
					'add' => 'create',
					'view' => 'read',
					'edit' => 'update',
					'delete' => 'delete'
				);

				return $this->Acl->check(array('User' => $user), 'forum.' . $controller, $crud[$action]);
			break;
		}

		return true;
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
		$this->layout = $this->config['viewLayout'];

		// Admin
		if (isset($this->request->params['admin'])) {
			$this->layout = 'admin';
		}

		// Localization
		$locale = $this->Auth->user('locale') ?: $this->settings['defaultLocale'];
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
