<?php
/** 
 * Forum - Reports Controller
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class ReportsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public  
	 * @var array
	 */
	public $uses = array('Forum.Report');  
	
	/**
	 * Pagination.
	 *
	 * @access public     
	 * @var array      
	 */ 
	public $paginate = array(  
		'Report' => array(
			'order' => array('Report.created' => 'ASC'),
			'limit' => 25,
			'contain' => false
		) 
	);
	
	/**
	 * List of all reports.
	 * 
	 * @param int $type
	 */
	public function admin_index($type = 0) {
		if ($type == Report::TOPIC) {
			$this->setAction('admin_topics');
			
		} else if ($type == Report::POST) {
			$this->setAction('admin_posts');
			
		} else if ($type == Report::USER) {
			$this->setAction('admin_users');
		
		} else {
			$this->paginate['Report']['contain'] = array('Reporter', 'Topic', 'Post', 'User');

			$this->Toolbar->pageTitle(__d('forum', 'Reported Items', true));
			$this->set('reports', $this->paginate('Report'));
		}
	}
	
	/**
	 * Reported topics.
	 */
	public function admin_topics() {
		if (!empty($this->data)) {
			if (!empty($this->data['Report']['items'])) {
				$this->loadModel('Forum.Topic');
				
				foreach ($this->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);
					
					switch ($this->data['Report']['action']) {
						case 'delete':
							$this->Topic->delete($item_id, true);
						break;
						case 'close':
							$this->Topic->id = $item_id;
							$this->Topic->saveField('status', Topic::STATUS_CLOSED);
						break;
					}
					
					$this->Report->delete($report_id, true);
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topics have been processed', true), count($this->data['Report']['items'])));
			}
		}
		
		$this->paginate['Report']['conditions']['Report.itemType'] = Report::TOPIC;
		$this->paginate['Report']['contain']= array('Reporter', 'Topic');
		
		$this->Toolbar->pageTitle(__d('forum', 'Reported Topics', true));
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Reported posts.
	 */
	public function admin_posts() {
		if (!empty($this->data)) {
			if (!empty($this->data['Report']['items'])) {
				$this->loadModel('Forum.Post');
				
				foreach ($this->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);
					
					switch ($this->data['Report']['action']) {
						case 'delete':
							$this->Post->delete($item_id);
						break;
					}
					
					$this->Report->delete($report_id, true);
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d posts have been processed', true), count($this->data['Report']['items'])));
			}
		}
		
		$this->paginate['Report']['conditions']['Report.itemType'] = Report::POST;
		$this->paginate['Report']['contain'] = array('Reporter', 'Post');
		
		$this->Toolbar->pageTitle(__d('forum', 'Reported Posts', true));
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Reported users.
	 */
	public function admin_users() {
		if (!empty($this->data)) {
			if (!empty($this->data['Report']['items'])) {
				$this->loadModel('User');
				
				foreach ($this->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);
					
					switch ($this->data['Report']['action']) {
						case 'ban':
							$this->User->id = $item_id;
							$this->User->saveField($this->config['userMap']['status'], $this->config['statusMap']['banned']);
						break;
					}
					
					$this->Report->delete($report_id, true);
				}
				
				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d users have been processed', true), count($this->data['Report']['items'])));
			}
		}
		
		$this->paginate['Report']['conditions']['Report.itemType'] = Report::USER;
		$this->paginate['Report']['contain']= array('Reporter', 'User');
		
		$this->Toolbar->pageTitle(__d('forum', 'Reported Users', true));
		$this->set('reports', $this->paginate('Report'));
	}
	
	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Security->disabledFields = array('items');
		$this->set('menuTab', 'reports');
	}
	
}
