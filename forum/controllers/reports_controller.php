<?php
/** 
 * reports_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Reports Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum 
 */
 
class ReportsController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public  
	 * @var string
	 */
	public $name = 'Reports';

	/**
	 * Models
	 * @access public  
	 * @var array
	 */
	public $uses = array('Forum.Report');  
	
	/**
	 * Pagination 
	 * @access public     
	 * @var array      
	 */ 
	public $paginate = array(  
		'Report' => array(
			'order' => 'Report.created ASC',
			'limit' => 25,
			'contain' => false
		) 
	);
	
	/**
	 * List of all reports
	 * @access public
	 * @category Admin
	 */
	public function admin_index() {
		$this->paginate['Report']['contain'] = array('Reporter.id', 'Reporter.username', 'Topic.title', 'Topic.id', 'Post', 'User.id', 'User.username');
		
		$this->pageTitle = __d('forum', 'Reported Items', true);
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Reported topics
	 * @access public
	 * @category Admin
	 */
	public function admin_topics() {
		if (!empty($this->data)) {
			if (!empty($this->data['Report']['items'])) {
				$this->loadModel('Forum.Topic');
				
				$counter = 0;
				foreach ($this->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);
					
					switch ($this->data['Report']['action']) {
						case 'delete':
							$this->Topic->destroy($item_id);
						break;
						case 'close':
							$this->Topic->id = $item_id;
							$this->Topic->saveField('status', 1);
						break;
					}
					
					$this->Report->delete($report_id, true);
					++$counter;
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topics have been processed', true), $counter));
			}
		}
		
		// Paginate
		$this->paginate['Report']['conditions']['Report.itemType'] = 'topic';
		$this->paginate['Report']['contain']= array('Reporter.id', 'Reporter.username', 'Topic.title', 'Topic.id');
		
		$this->pageTitle = __d('forum', 'Reported Topics', true);
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Reported posts
	 * @access public
	 * @category Admin
	 */
	public function admin_posts() {
		if (!empty($this->data)) {
			if (!empty($this->data['Report']['items'])) {
				$this->loadModel('Forum.Post');
				
				$counter = 0;
				foreach ($this->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);
					
					switch ($this->data['Report']['action']) {
						case 'delete':
							$this->Post->destroy($item_id);
						break;
					}
					
					$this->Report->delete($report_id, true);
					++$counter;
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d posts have been processed', true), $counter));
			}
		}
		
		// Paginate
		$this->paginate['Report']['conditions']['Report.itemType'] = 'post';
		$this->paginate['Report']['contain'] = array('Reporter.id', 'Reporter.username', 'Post');
		
		$this->pageTitle = __d('forum', 'Reported Posts', true);
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Reported users
	 * @access public
	 * @category Admin
	 */
	public function admin_users() {
		if (!empty($this->data)) {
			if (!empty($this->data['Report']['items'])) {
				$this->loadModel('Forum.User');
				
				$counter = 0;
				foreach ($this->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);
					
					switch ($this->data['Report']['action']) {
						case 'delete':
							$this->User->delete($item_id, true);
						break;
						case 'ban':
							$this->User->id = $item_id;
							$this->User->saveField('status', 1);
						break;
					}
					
					$this->Report->delete($report_id, true);
					++$counter;
				}
				
				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d users have been processed', true), $counter));
			}
		}
		
		// Paginate
		$this->paginate['Report']['conditions']['Report.itemType'] = 'user';
		$this->paginate['Report']['contain']= array('Reporter.id', 'Reporter.username', 'User.id', 'User.username');
		
		$this->pageTitle = __d('forum', 'Reported Users', true);
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Security->disabledFields = array('items');
		
		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
			$this->set('menuTab', 'reports');
		}
	}
	
}
