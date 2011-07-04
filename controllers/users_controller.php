<?php
/** 
 * Forum - Users Controller
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class UsersController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('User', 'Forum.Profile');  

	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('Email');   
	
	/**
	 * Pagination.
	 *
	 * @access public   
	 * @var array      
	 */ 
	public $paginate = array(  
		'Profile' => array(
			'contain' => array('User'),
			'limit' => 25
		) 
	);
	
	/**
	 * List of users.
	 */
	public function index() {
		if (!empty($this->data['Profile']['username'])) {
			$this->paginate['Profile']['conditions'] = array('User.'. $this->config['userMap']['username'] .' LIKE' => '%'. Sanitize::clean($this->data['Profile']['username']) .'%');
		}
		
		$this->paginate['Profile']['order'] = array('User.'. $this->config['userMap']['username'] => 'ASC');
		
		$this->Toolbar->pageTitle(__d('forum', 'User List', true));
		$this->set('users', $this->paginate('Profile'));
	}
	
	/**
	 * Edit a forum profile.
	 */
	public function edit() {
		$user_id = $this->Auth->user('id');
		$profile = $this->Profile->getUserProfile($user_id);
		
		if (!empty($this->data)) {
			$this->Profile->id = $user_id;

			if ($this->Profile->save($this->data, false)) {
				$this->Session->setFlash(__d('forum', 'Your profile information has been updated!', true));
			}
		} else {
			$this->data = $profile;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Profile', true));
	}

	/**
	 * Login.
	 */
	public function login() {
		if (!empty($this->data)) {
			$this->User->set($this->data);
			
			if ($this->User->validates()) {
				if ($user = $this->Auth->user()) {
					$this->Profile->login($user['User']['id']);
					$this->Session->delete('Forum');
					$this->redirect($this->Auth->loginRedirect);
				}
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Login', true));
	}
	
	/**
	 * Logout.
	 */
	public function logout() {
		$this->Session->delete('Forum');
		$this->redirect($this->Auth->logout());
	}

	/**
	 * User profile.
	 *
	 * @param int $user_id
	 */
	public function profile($user_id) {
		$user = $this->Profile->getByUser($user_id);
		
		if (empty($user)) {
			return $this->cakeError('error404');
		}
		
		$this->loadModel('Forum.Topic');

		$this->Toolbar->pageTitle(__d('forum', 'User Profile', true), $user['User'][$this->config['userMap']['username']]);
		$this->set('user', $user);
		$this->set('topics', $this->Topic->getLatestByUser($user_id));
		$this->set('posts', $this->Topic->Post->getLatestByUser($user_id));
	}
	
	/**
	 * Report a user.
	 *
	 * @param int $user_id
	 */
	public function report($user_id) {
		$user = $this->Profile->getByUser($user_id);
		
		if (empty($user)) {
			return $this->cakeError('error404');
		}
		
		$this->loadModel('Forum.Report');

		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $this->Auth->user('id');
			$this->data['Report']['item_id'] = $user_id;
			$this->data['Report']['itemType'] = Report::USER;
			
			if ($this->Report->save($this->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have succesfully reported this user! A moderator will review this topic and take the necessary action.', true));
				unset($this->data['Report']);
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Report User', true));
		$this->set('user', $user);
	}
	
	/**
	 * Admin index!
	 */
	public function admin_index() {
		if (!empty($this->data)) {
			if (!empty($this->data['Profile']['username'])) {
				$this->paginate['Profile']['conditions']['User.'. $this->config['userMap']['username'] .' LIKE'] = '%'. Sanitize::clean($this->data['Profile']['username']) .'%';
			}
			
			if (!empty($this->data['Profile']['id'])) {
				$this->paginate['Profile']['conditions']['User.id'] = $this->data['Profile']['id'];
			}
		}
		
		$this->paginate['Profile']['order'] = array('User.'. $this->config['userMap']['username'] => 'ASC');

		$this->Toolbar->pageTitle(__d('forum', 'Manage Users', true));
		$this->set('users', $this->paginate('Profile'));
	}
	
	/**
	 * Edit a user.
	 * 
	 * @param int $id
	 */
	public function admin_edit($id) {
		$profile = $this->Profile->get($id);
		
		if (empty($profile)) {
			return $this->cakeError('error404');
		}
		
		// Form Processing
		if (!empty($this->data)) {
			$this->Profile->id = $id;
			
			if ($this->Profile->save($this->data, true)) {
				$this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $profile;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit User', true));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'login', 'logout', 'profile');
		
		$this->set('menuTab', 'users');
	}

}