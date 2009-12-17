<?php
/** 
 * users_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Users Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum 
 */
 
class UsersController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public
	 * @var string
	 */
	public $name = 'Users';

	/**
	 * Models
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.User');  

	/**
	 * Components
	 * @access public
	 * @var array
	 */
	public $components = array('Email');   
	
	/**
	 * Pagination   
	 * @access public   
	 * @var array      
	 */ 
	public $paginate = array(  
		'User' => array(
			'order' => 'User.username ASC',
			'limit' => 25,
			'contain' => false
		) 
	);
	
	/**
	 * Redirect 
	 * @access public 
	 */
	public function index() {
		$this->Toolbar->goToPage();
	}
	
	/**
	 * Edit a users profile
	 * @access public
	 */
	public function edit() {
		$user_id = $this->Auth->user('id');
		$user = $this->User->get($user_id);
		
		// Form Processing
		if (!empty($this->data)) {
			$this->User->id = $user_id;
			$this->User->set($this->data);
			
			if ($this->User->validates()) {
				if (isset($this->data['User']['newPassword'])) {
					$this->data['User']['password'] = $this->Auth->password($this->data['User']['newPassword']);
				}

				$this->User->id = $user_id;
				if ($this->User->save($this->data, false, array('email', 'password', 'signature', 'locale', 'timezone'))) {
					$this->Session->setFlash(__d('forum', 'Your profile information has been updated!', true));

					foreach ($this->data['User'] as $field => $value) {
						$this->_refreshAuth($field, $value);
					}
				}
			}
		}
		
		foreach ($user['User'] as $field => $value) {
			if (empty($this->data['User'][$field])) {
				$this->data['User'][$field] = $value;
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Profile', true));
	}
	
	/**
	 * Forgot credentials
	 * @access public
	 */
	public function forgot() {
		if (!empty($this->data)) {
			if ($user = $this->User->forgotRetrieval($this->data)) {
				$this->Toolbar->resetPassword($user);
				
				$this->Session->setFlash(__d('forum', 'Your new password and information has been sent to your email.', true));
				unset($this->data['User']);
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Forgot Password', true));
	}
	
	/**
	 * Login
	 * @access public
	 */
	public function login() {
		if (!empty($this->data)) {
			$this->User->set($this->data);
			$this->User->action = 'login';
			
			if ($this->User->validates()) {
				if ($user = $this->Auth->user()) {
					$this->User->login($user);
					$this->Session->delete('Forum');
					$this->redirect($this->Auth->loginRedirect);
				}
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Login', true));
	}
	
	/**
	 * Logout
	 * @access public
	 */
	public function logout() {
		$this->Session->delete('Forum');
		$this->redirect($this->Auth->logout());
	}
	
	/**
	 * List of all users
	 * @access public
	 */
	public function listing() {
		if (!empty($this->data)) {
			if (!empty($this->data['User']['username'])) {
				$this->paginate['User']['conditions']['User.username LIKE'] = '%'. $this->data['User']['username'] .'%';
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'User List', true));
		$this->set('users', $this->paginate('User'));
	}
	
	/**
	 * User profile
	 * @access public
	 * @param int $id
	 */
	public function profile($id) {
		$user = $this->User->getProfile($id);
		
		if (!empty($user)) {
			$this->loadModel('Forum.Topic');
			$this->set('topics', $this->Topic->getLatestByUser($id));
			$this->set('posts', $this->Topic->Post->getLatestByUser($id));
		}
	
		$this->Toolbar->pageTitle(__d('forum', 'User Profile', true), $user['User']['username']);
		$this->set('user', $user);
	}
	
	/**
	 * Report a user
	 * @access public
	 * @param int $id
	 */
	public function report($id) {
		$this->loadModel('Forum.Report');
		
		$user = $this->User->get($id, array('id', 'username'));
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array('exists' => $user));
		
		// Submit Report
		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $user_id;
			$this->data['Report']['item_id'] = $id;
			$this->data['Report']['itemType'] = 'user';
			
			if ($this->Report->save($this->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have succesfully reported this user! A moderator will review this topic and take the necessary action.', true));
				unset($this->data['Report']);
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Report User', true));
		$this->set('id', $id);
		$this->set('user', $user);
	}
	
	/**
	 * Signup
	 * @access public
	 */
	public function signup() {
		if (!empty($this->data)) {
			$this->User->create();
			$this->User->set($this->data);
			$this->User->action = 'signup';
			
			if ($this->User->validates()) {
				$this->data['User']['username'] = strip_tags($this->data['User']['username']);
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['newPassword']);
				
				if ($this->User->save($this->data, false, array('username', 'email', 'password'))) {
					$this->Session->setFlash(__d('forum', 'You have successfully signed up, you may now login and begin posting.', true));

					// Send email
					$message  = sprintf(__d('forum', 'Thank you for signing up on %s, your information is listed below', true), $this->Toolbar->settings['site_name']) .":\n\n";
					$message .= __d('forum', 'Username', true) .": ". $this->data['User']['username'] ."\n";
					$message .= __d('forum', 'Password', true) .": ". $this->data['User']['newPassword'] ."\n\n";
					$message .= __d('forum', 'Enjoy!', true);
					
					$this->Email->to = $this->data['User']['email'];
					$this->Email->from = $this->Toolbar->settings['site_name'] .' <'. $this->Toolbar->settings['site_email'] .'>';
					$this->Email->subject = $this->Toolbar->settings['site_name'] .' - '. __d('forum', 'Sign Up Confirmation', true);
					$this->Email->send($message);
					
					unset($this->data['User']);
				}
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Sign Up', true));
	}
	
	/**
	 * Admin index!
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
	 * Edit a user
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
			
			if ($this->User->save($this->data, true, array('username', 'email', 'totalPosts', 'totalTopics'))) {
				$this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $user;
		}
		
		$this->pageTitle = __d('forum', 'Edit User', true);
		$this->set('id', $id);
	}
	
	/**
	 * Reset users password
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
	 * Delets a user and all its content
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
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'forgot', 'login', 'logout', 'listing', 'profile', 'signup');

		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
		}
		
		$this->set('menuTab', 'users');
	}

}