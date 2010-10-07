<?php
/** 
 * Forum - Home Controller
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class ForumController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public  
	 * @var array
	 */
	public $uses = array('Forum.Topic', 'Forum.Profile');

	/**
	 * Forum index.
	 *
	 * @access public   
	 */
	public function index() {
		$this->Toolbar->pageTitle(__d('forum', 'Index', true));
		$this->set('menuTab', 'home');
		$this->set('forums', 		$this->Topic->ForumCategory->Forum->getIndex($this->Toolbar->getAccess(), $this->Session->read('Forum.access')));
		$this->set('totalPosts', 	$this->Topic->Post->getTotal());
		$this->set('totalTopics', 	$this->Topic->getTotal());
		$this->set('totalUsers', 	$this->Topic->User->getTotal());
		$this->set('newestUser', 	$this->Profile->getNewestUser());
		$this->set('whosOnline', 	$this->Profile->whosOnline($this->Toolbar->settings['whos_online_interval']));
	}
	
	/**
	 * RSS Feed.
	 *
	 * @access public
	 */
	public function feed() {
		if ($this->RequestHandler->isRss()) {
			$this->set('items', $this->Topic->getLatest());
			$this->set('document', array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
		} else {
			$this->redirect('/forum/home/feed.rss');
		}
	}
	
	/**
	 * Help / FAQ.
	 *
	 * @access public
	 */
	public function help() {
		$this->Toolbar->pageTitle(__d('forum', 'Help', true));
		$this->set('menuTab', 'help');
	}
	
	/**
	 * Rules.
	 *
	 * @access public
	 */
	public function rules() {
		$this->Toolbar->pageTitle(__d('forum', 'Rules', true));
		$this->set('menuTab', 'rules');
	}
	
	/**
	 * Administration home, list statistics.
	 *
	 * @access public
	 * @category Admin
	 */
	public function admin_index() {
		$this->loadModel('Forum.Report');
		$this->loadModel('Forum.Moderator');
		
		$this->pageTitle = __d('forum', 'Administration', true);
		$this->set('menuTab', 'home');
		$this->set('totalPosts', 	$this->Topic->Post->getTotal());
		$this->set('totalTopics', 	$this->Topic->getTotal());
		$this->set('totalUsers', 	$this->Topic->User->getTotal());
		$this->set('totalPolls', 	$this->Topic->Poll->getTotal());
		$this->set('totalReports', 	$this->Report->getTotal());
		$this->set('totalMods', 	$this->Moderator->getTotal());
		$this->set('newestUser', 	$this->Topic->User->getNewestUser());
		$this->set('latestReports', $this->Report->getLatest());
		$this->set('latestUsers', 	$this->Topic->User->getLatest());
	}
	
	/**
	 * Edit the settings.
	 *
	 * @access public
	 * @category Admin
	 */
	public function admin_settings() {
		$this->loadModel('Forum.Setting');
		
		// Form Processing
		if (!empty($this->data)) {
			$this->Setting->set($this->data);
			
			if ($this->Setting->save($this->data, true)) {
				$this->Session->setFlash(__d('forum', 'Settings have been updated!', true));
			}
		} else {
			$this->data['Setting'] = $this->Toolbar->settings;
		}
		
		$this->pageTitle = __d('forum', 'Settings', true);
		$this->set('menuTab', 'settings');
	}
	
	/**
	 * Before filter.
	 * 
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('*');
		
		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
		}
	}

}
