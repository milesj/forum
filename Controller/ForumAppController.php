<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

/**
 * @property ForumToolbarComponent $ForumToolbar
 * @property AdminToolbarComponent $AdminToolbar
 */
class ForumAppController extends AppController {

	/**
	 * Remove parent models.
	 *
	 * @type array
	 */
	public $uses = array();

	/**
	 * Components.
	 *
	 * @type array
	 */
	public $components = array(
		'Session', 'Security', 'Cookie', 'Acl',
		'Auth' => array(
			'authorize' => array('Controller')
		),
		'Forum.ForumToolbar',
		'Admin.AdminToolbar'
	);

	/**
	 * Helpers.
	 *
	 * @type array
	 */
	public $helpers = array(
		'Html', 'Session', 'Form', 'Time', 'Text',
		'Utility.Breadcrumb', 'Utility.OpenGraph',
		'Utility.Utility', 'Utility.Decoda',
		'Forum.Forum'
	);

	/**
	 * Plugin configuration.
	 *
	 * @type array
	 */
	public $config = array();

	/**
	 * Database forum settings.
	 *
	 * @type array
	 */
	public $settings = array();

	/**
	 * Validate the user has the correct ACL permissions.
	 *
	 * @param array $user
	 * @return bool
	 * @throws UnauthorizedException
	 */
	public function isAuthorized($user) {
		if ($this->Session->read('Acl.isAdmin')) {
			return true;
		}

		$controller = strtolower($this->name);
		$action = $this->request->params['action'];
		$model = 'Forum.';

		// Change to polls when applicable
		if (isset($this->request->params['pass'][1]) && $this->request->params['pass'][1] === 'poll') {
			$controller = 'polls';
		}

		// Allow for controllers that don't have ACL
		if (!in_array($controller, array('stations', 'topics', 'posts', 'polls'))) {
			return true;
		}

		switch ($controller) {
			case 'stations':	$model .= 'Forum'; break;
			case 'topics':		$model .= 'Topic'; break;
			case 'posts':		$model .= 'Post'; break;
			case 'polls':		$model .= 'Poll'; break;
		}

		// Validate based on action
		switch ($action) {

			// Allow if the user belongs to admin or super
			case 'moderate':
				if ($this->Session->read('Acl.isSuper') || $this->Session->read('Forum.moderates')) {
					return true;
				}
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

				$has = $this->AdminToolbar->hasAccess($model, $crud[$action], 'Forum.permissions', true);

				// If permission doesn't exist, they have it by default
				if ($has === null || $has) {
					return true;
				}
			break;

			// All other actions should be available
			default:
				return true;
			break;
		}

		throw new UnauthorizedException();
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->set('menuTab', '');

		// Settings
		$this->config = Configure::read();
		$this->settings = Configure::read('Forum.settings');
		$this->layout = $this->config['Forum']['viewLayout'];

		// Localization
		$locale = $this->Auth->user(Configure::read('User.fieldMap.locale')) ?: $this->settings['defaultLocale'];
		Configure::write('Config.language', $locale);
	}

	/**
	 * Before render.
	 */
	public function beforeRender() {
		$this->set('user', $this->Auth->user());
		$this->set('userFields', $this->config['User']['fieldMap']);
		$this->set('userRoutes', $this->config['User']['routes']);
		$this->set('config', $this->config);
		$this->set('settings', $this->settings);
	}

}
