<?php
/** 
 * Forum - Search Controller
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class SearchController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Topic');
	
	/**
	 * Pagination.
	 *
	 * @access public
	 * @var array 
	 */
	public $paginate = array( 
		'Topic' => array(
			'order' => array('LastPost.created' => 'DESC'),
			'contain' => array('Forum', 'User', 'LastPost', 'LastUser', 'Poll', 'FirstPost')
		)
	); 
	
	/**
	 * Search the topics.
	 *
	 * @param string $type
	 */
	public function index($type = '') {
		$searching = false;
		$forums = $this->Topic->Forum->getGroupedHierarchy('accessRead');
		
		if (!empty($this->params['named'])) {
			foreach ($this->params['named'] as $field => $value) {
				$this->data['Topic'][$field] = urldecode($value);
			}
		}
		
		if ($type == 'new_posts') {
			$this->data['Topic']['orderBy'] = 'LastPost.created';
			$this->paginate['Topic']['conditions']['LastPost.created >='] = $this->Session->read('Forum.lastVisit');
		}

		if (!empty($this->data)) {
			$searching = true;
			
			if (!empty($this->data['Topic']['keywords'])) {
				$keywords = Sanitize::clean($this->data['Topic']['keywords']);
			
				if ($this->data['Topic']['power'] == 0) {
					$this->paginate['Topic']['conditions']['Topic.title LIKE'] = '%'. $keywords .'%';
				} else {
					$this->paginate['Topic']['conditions']['OR'] = array(
						array('Topic.title LIKE' => '%'. $keywords .'%'),
						array('FirstPost.content LIKE' => '%'. $keywords .'%')
					);
				}
			}

			if (empty($this->data['Topic']['forum_id'])) {
				$this->data['Topic']['forum_id'] = array();
				
				foreach ($forums as $forum_ids) {
					$this->data['Topic']['forum_id'] = array_keys($forum_ids) + $this->data['Topic']['forum_id'];
				}
			}
			
			if (empty($this->data['Topic']['orderBy'])) {
				$this->data['Topic']['orderBy'] = 'LastPost.created';
			}

			if (!empty($this->data['Topic']['byUser'])) {
				$this->paginate['Topic']['conditions']['User.'. $this->config['userMap']['username'] .' LIKE'] = '%'. $this->data['Topic']['byUser'] .'%';
			}

			$this->paginate['Topic']['conditions']['Forum.accessRead <='] = $this->Session->read('Forum.access');
			$this->paginate['Topic']['conditions']['Topic.forum_id'] = $this->data['Topic']['forum_id'];
			$this->paginate['Topic']['order'] = array($this->data['Topic']['orderBy'] => 'DESC');
			$this->paginate['Topic']['limit'] = $this->settings['topics_per_page'];
			
			$this->set('topics', $this->paginate('Topic'));
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Search', true));
		$this->set('menuTab', 'search');
		$this->set('searching', $searching);
		$this->set('forums', $forums);
	}
	
	/**
	 * Proxy action to build named parameters.
	 */
	public function proxy() {
		$named = array();
		
		foreach ($this->data['Topic'] as $field => $value) {
			if ($value != '') {
				$named[$field] = urlencode(htmlentities($value, ENT_NOQUOTES, 'UTF-8'));
			}	
		}
		
		$this->redirect(array_merge(array('controller' => 'search', 'action' => 'index'), $named));
	}
	
	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('*');
	}

}
