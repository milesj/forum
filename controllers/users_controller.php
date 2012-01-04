<?php
/** 
 * Forum - UsersController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
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
	 * Helpers.
	 * 
	 * @access public
	 * @var array
	 */
	public $helpers = array('Utils.Gravatar');
	
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
		if (!empty($this->params['named']['username'])) {
			$this->data['Profile']['username'] = $this->params['named']['username'];
			$this->paginate['Profile']['conditions'] = array('User.'. $this->config['userMap']['username'] .' LIKE' => '%'. Sanitize::clean($this->params['named']['username']) .'%');
		}
		
		$this->paginate['Profile']['order'] = array('User.'. $this->config['userMap']['username'] => 'ASC');
		
		$this->Toolbar->pageTitle(__d('forum', 'User List', true));
		$this->set('users', $this->paginate('Profile'));
	}
	
	/**
	 * Proxy action to build named parameters.
	 */
	public function proxy() {
		$named = array();

		foreach ($this->data['Profile'] as $field => $value) {
			if ($value != '') {
				$named[$field] = urlencode($value);
			}	
		}
		
		$this->redirect(array_merge(array('controller' => 'users', 'action' => 'index'), $named));
	}
	
	/**
	 * Dashboard and activity.
	 */
	public function dashboard() {
		$this->loadModel('Forum.Topic');
		$this->loadModel('Forum.Subscription');
		
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->pageTitle(__d('forum', 'Dashboard', true));
		$this->set('topics', $this->Topic->getLatestByUser($user_id));
		$this->set('activity', $this->Topic->Post->getGroupedLatestByUser($user_id));
		$this->set('subscriptions', $this->Subscription->getTopicSubscriptionsByUser($user_id));
	}
	
	/**
	 * Edit a forum profile.
	 */
	public function edit() {
		$user_id = $this->Auth->user('id');
		$profile = $this->Profile->getUserProfile($user_id);
		
		if (!empty($this->data)) {
			$this->Profile->id = $profile['Profile']['id'];

			if ($this->Profile->save($this->data, true)) {
				$this->Session->setFlash(__d('forum', 'Your profile information has been updated!', true));
			}
		} else {
			$this->data = $profile;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Profile', true));
	}

	/**
	 * User profile.
	 *
	 * @param int $user_id
	 */
	public function profile($user_id) {
		$profile = $this->Profile->getByUser($user_id);
		
		if (empty($profile)) {
			return $this->cakeError('error404');
		}
		
		$this->loadModel('Forum.Topic');

		$this->Toolbar->pageTitle(__d('forum', 'User Profile', true), $profile['User'][$this->config['userMap']['username']]);
		$this->set('profile', $profile);
		$this->set('topics', $this->Topic->getLatestByUser($user_id));
		$this->set('posts', $this->Topic->Post->getLatestByUser($user_id));
	}
	
	/**
	 * Report a user.
	 *
	 * @param int $user_id
	 */
	public function report($user_id) {
		$profile = $this->Profile->getByUser($user_id);
		
		if (empty($profile)) {
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
		
		$this->Toolbar->pageTitle(__d('forum', 'Report User', true), $profile['User'][$this->config['userMap']['username']]);
		$this->set('profile', $profile);
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
		
		if (!empty($this->data)) {
			$this->Profile->id = $id;
			
			if ($this->Profile->save($this->data, true)) {
				$this->Session->setFlash(sprintf(__d('forum', 'Profile for %s has been updated.', true), '<strong>'. $profile['User']['username'] .'</strong>'));
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
		
		$this->Auth->allow('index', 'login', 'logout', 'profile', 'proxy');
		
		$this->set('menuTab', 'users');
	}

}