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
			'order' => array('User.username' => 'ASC'),
			'contain' => array('User'),
			'limit' => 25
		) 
	);
	
	/**
	 * List of users.
	 */
	public function index() {
		if (!empty($this->data['User']['username'])) {
			$this->paginate['Profile']['conditions']['User.username LIKE'] = '%'. Sanitize::clean($this->data['User']['username']) .'%';
		}
		
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
	 * @param int $id
	 */
	public function profile($id) {
		$user = $this->Profile->get($id);
		
		if (empty($user)) {
			return $this->cakeError('error404');
		}
		
		$this->loadModel('Forum.Topic');

		$this->Toolbar->pageTitle(__d('forum', 'User Profile', true), $user['User'][$this->config['userMap']['username']]);
		$this->set('user', $user);
		$this->set('topics', $this->Topic->getLatestByUser($id));
		$this->set('posts', $this->Topic->Post->getLatestByUser($id));
	}
	
	/**
	 * Report a user.
	 *
	 * @param int $id
	 */
	public function report($id) {
		$user_id = $this->Auth->user('id');
		$user = $this->Profile->get($id);
		
		if (empty($user)) {
			return $this->cakeError('error404');
		}
		
		$this->loadModel('Forum.Report');

		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $user_id;
			$this->data['Report']['item_id'] = $id;
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
	 *
	 * @access public
	 * @category Admin
	 */
	public function admin_index() {
		if (!empty($this->data)) {
			if (!empty($this->data['User']['username'])) {
				$this->paginate['User']['conditions']['User.username LIKE'] = '%'. $this->data['User']['username'] .'%';
			}
			
			if (!empty($this->data['User']['id'])) {
				$this->paginate['User']['conditions']['User.id'] = $this->data['User']['id'];
			}
		}

		$this->pageTitle = __d('forum', 'Manage Users', true);
		$this->set('users', $this->paginate('User'));
	}
	
	/**
	 * Edit a user.
	 *
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_edit($id) {
		$user = $this->User->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $user));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->User->id = $id;
			
			if ($this->User->save($this->data, true, array('username', 'email', $this->User->columnMap['totalPosts'], $this->User->columnMap['totalTopics']))) {
				$this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $user;
		}
		
		$this->pageTitle = __d('forum', 'Edit User', true);
		$this->set('id', $id);
	}
	
	/**
	 * Reset users password.
	 *
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_reset($id) {
		$user = $this->User->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $user));
		
		if (!empty($user)) {
			$this->Toolbar->resetPassword($user, true);
			$this->Session->setFlash(sprintf(__d('forum', 'The password for %s has been reset!', true), '<strong>'. $user['User']['username'] .'</strong>'));
		}
		
		$this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => true));
	}
	
	/**
	 * Delets a user and all its content.
	 *
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_delete($id) {
		$user = $this->User->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $user));
		
		// Form Processing
		if (!empty($this->data['User']['delete'])) {
			$this->User->delete($id, true);

			$this->Session->setFlash(sprintf(__d('forum', 'The user %s and all of their associations have been deleted!', true), '<strong>'. $user['User']['username'] .'</strong>'));
			$this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => true));
		}
		
		$this->pageTitle = __d('forum', 'Delete User', true);
		$this->set('id', $id);
		$this->set('user', $user);
	}
	
	/**
	 * Before filter.
	 * 
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'login', 'logout', 'profile', 'signup');

		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
		}
		
		$this->set('menuTab', 'users');
	}

}