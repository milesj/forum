<?php
/** 
 * search_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Search Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class SearchController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public
	 * @var string
	 */
	public $name = 'Search';

	/**
	 * Models
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Topic');
	
	/**
	 * Pagination 
	 * @access public
	 * @var array 
	 */
	public $paginate = array( 
		'Topic' => array(
			'order' => 'LastPost.created DESC',
			'contain' => array('ForumCategory.title', 'User.id', 'User.username', 'LastPost.created', 'LastUser.username', 'Poll.id', 'FirstPost.content')
		)
	); 
	
	/**
	 * Search the topics
	 * @access public
	 * @param string $type
	 */
	public function index($type = '') {
		$searching = false;
		
		// Build
		if (!empty($this->params['named'])) {
			foreach ($this->params['named'] as $field => $value) {
				$this->data['Topic'][$field] = urldecode($value);
			}
		}
		
		if ($type == 'new_posts') {
			$this->data['Topic']['orderBy'] = 'LastPost.created';
			$this->paginate['Topic']['conditions']['LastPost.created >='] = $this->Session->read('Forum.lastVisit');
		}
		
		// Search
		if (!empty($this->data)) {
			$searching = true;
			$this->paginate['Topic']['limit'] = $this->Toolbar->settings['topics_per_page'];
			
			if (!empty($this->data['Topic']['keywords'])) {
				if ($this->data['Topic']['power'] == 0) {
					$this->paginate['Topic']['conditions']['Topic.title LIKE'] = '%'. $this->data['Topic']['keywords'] .'%';
				} else {
					$this->paginate['Topic']['conditions']['OR'] = array(
						array('Topic.title LIKE' => '%'. $this->data['Topic']['keywords'] .'%'),
						array('FirstPost.content LIKE' => '%'. $this->data['Topic']['keywords'] .'%')
					);
				}
			}
			
			if (!empty($this->data['Topic']['category'])) {
				$this->paginate['Topic']['conditions']['Topic.forum_category_id'] = $this->data['Topic']['category'];
			}
			
			if (!empty($this->data['Topic']['orderBy'])) {
				$this->paginate['Topic']['order'] = $this->data['Topic']['orderBy'] .' DESC';
			}
			
			if (!empty($this->data['Topic']['byUser'])) {
				$this->paginate['Topic']['conditions']['User.username LIKE'] = '%'. $this->data['Topic']['byUser'] .'%';
			}
			
			$this->set('topics', $this->paginate('Topic'));
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Search', true));
		$this->set('menuTab', 'search');
		$this->set('searching', $searching);
		$this->set('forums', $this->Topic->ForumCategory->getHierarchy($this->Toolbar->getAccess(), $this->Session->read('Forum.access'), 'read'));
	}
	
	/**
	 * Proxy action to build named parameters
	 * @access public
	 */
	public function proxy() {
		$named = array();
		foreach ($this->data['Topic'] as $field => $value) {
			if ($value != '') {
				$named[$field] = urlencode(htmlentities($value, ENT_NOQUOTES, 'UTF-8'));
			}	
		}
		
		$destination = array_merge(array('controller' => 'search', 'action' => 'index'), $named);
		$this->redirect($destination);
	}
	
	/**
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('*');
	}

}
