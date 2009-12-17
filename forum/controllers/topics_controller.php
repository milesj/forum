<?php
/** 
 * topics_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Topics Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum  
 */
 
class TopicsController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public
	 * @var string
	 */
	public $name = 'Topics';

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
		'Post' => array(
			'order' => 'Post.created ASC',
			'contain' => array('User' => array('Access' => array('AccessLevel')))
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
	 * Post a new topic or poll
	 * @access public
	 * @param int $category_id
	 * @param string $type
	 */
	public function add($category_id, $type = '') {
		$category = $this->Topic->ForumCategory->getCategory($category_id);
		$user_id = $this->Auth->user('id');
		
		if ($type == 'poll') {
			$pageTitle = __d('forum', 'Post Poll', true);
			$access = 'accessPoll';
			$isPoll = true;
		} else {
			$pageTitle = __d('forum', 'Post Topic', true);
			$access = 'accessPost';
			$isPoll = false;
		}
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $category, 
			'status' => $category['ForumCategory']['status'], 
			'permission' => $category['ForumCategory'][$access]
		));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->data['Topic']['user_id'] = $user_id;
			$this->data['Topic']['userIP'] = $this->RequestHandler->getClientIp();

			if ($topic_id = $this->Topic->addTopic($this->data, $this->Toolbar->settings, $this->Session->read('Forum.topics'), $isPoll)) {
				if ($category['ForumCategory']['settingPostCount'] == 1) {
					$this->Topic->User->increasePosts($user_id);
					$this->Topic->User->increaseTopics($user_id);
				}
				
				$this->Toolbar->updateTopics($topic_id);
				$this->Toolbar->goToPage($topic_id);
			}
		} else {
			$this->data['Topic']['forum_category_id'] = $category_id;
		}
		
		$this->Toolbar->pageTitle($pageTitle);
		$this->set('id', $category_id);
		$this->set('type', $type);
		$this->set('category', $category);
		$this->set('pageTitle', $pageTitle);
		$this->set('forums', $this->Topic->ForumCategory->getHierarchy($this->Toolbar->getAccess(), $this->Session->read('Forum.access'), 'post'));
	}
	
	/**
	 * Edit a topic
	 * @access public
	 * @param int $id
	 */
	public function edit($id) {
		$topic = $this->Topic->getTopicForEdit($id);
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'moderate' => $topic['Topic']['forum_category_id'],
			'ownership' => $topic['Topic']['user_id']
		));
		
		// Form Processing
		if (!empty($this->data)) {
			if ($this->Topic->saveAll($this->data, array('validate' => 'only'))) {
				if ($this->Topic->editTopic($id, $this->data)) {
					$this->Toolbar->goToPage($id);
				}
			}
		} else {
			$this->data = $topic;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Topic', true));
		$this->set('id', $id);
		$this->set('topic', $topic);
		$this->set('forums', $this->Topic->ForumCategory->getHierarchy($this->Toolbar->getAccess(), $this->Session->read('Forum.access'), 'post'));
	}
	
	/**
	 * RSS Feed
	 * @access public
	 * @param int $id
	 */
	public function feed($id) {
		if ($this->RequestHandler->isRss()) {
			$topic = $this->Topic->get($id, null, array('FirstPost.content'));
			$this->Toolbar->verifyAccess(array('exists' => $topic));
		
			$this->paginate['Post']['limit'] = $this->Toolbar->settings['posts_per_page'];
			$this->paginate['Post']['conditions']['Post.topic_id'] = $id;
			$this->paginate['Post']['contain'] = array('User');
			$this->paginate['Post']['order'] = 'Post.created DESC';
			
			$this->set('items', $this->paginate('Post'));
			$this->set('topic', $topic);
			$this->set('document', array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
		} else {
			$this->redirect('/forum/topics/feed/'. $id .'.rss');
		}
	}
	
	/**
	 * Delete a topic
	 * @access public
	 * @param int $id
	 */
	public function delete($id) {
		$topic = $this->Topic->get($id, array('id', 'forum_category_id'));
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'moderate' => $topic['Topic']['forum_category_id']
		));
		
		// Delete All
		$this->Topic->destroy($id);
		$this->redirect(array('controller' => 'categories', 'action' => 'view', $topic['Topic']['forum_category_id']));
	}
	
	/**
	 * Report a topic
	 * @access public
	 * @param int $id
	 */
	public function report($id) {
		$this->loadModel('Forum.Report');
		
		$topic = $this->Topic->get($id, array('id', 'title'));
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array('exists' => $topic));
		
		// Submit Report
		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $user_id;
			$this->data['Report']['item_id'] = $id;
			$this->data['Report']['itemType'] = 'topic';
			
			if ($this->Report->save($this->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have succesfully reported this topic! A moderator will review this topic and take the necessary action.', true));
				unset($this->data['Report']);
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Report Topic', true));
		$this->set('id', $id);
		$this->set('topic', $topic);
	}

	/**
	 * Read a topic
	 * @access public
	 * @param int $id
	 */
	public function view($id) {
		$user_id = $this->Auth->user('id');
		$topic = $this->Topic->getTopicForViewing($id, $user_id);
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'permission' => $topic['ForumCategory']['accessRead']
		));
		
		// Update
		$this->Toolbar->markAsRead($id);
		$this->Topic->increaseViews($id);
		
		// Paginate
		$this->paginate['Post']['limit'] = $this->Toolbar->settings['posts_per_page'];
		$this->paginate['Post']['conditions']['Post.topic_id'] = $id;
		
		// Poll Voting
		if ($this->RequestHandler->isPost()) {
			if (!empty($this->data['Poll']['option'])) {
				$this->Topic->Poll->vote($topic['Poll']['id'], $this->data['Poll']['option'], $user_id);
				$this->redirect(array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $id));
			}
		}
		
		$this->Toolbar->pageTitle($topic['ForumCategory']['title'], $topic['Topic']['title']);
		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
	}
	
	/**
	 * Moderate a topic
	 * @access public
	 * @param int $id
	 */
	public function moderate($id) {
		$user_id = $this->Auth->user('id');
		$topic = $this->Topic->getTopicForViewing($id, $user_id);
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'permission' => $topic['ForumCategory']['accessRead'],
			'moderate' => $topic['Topic']['forum_category_id']
		));
		
		// Processing
		if ($this->RequestHandler->isPost()) {
			if (!empty($this->data['Post']['items'])) {
				$items = $this->data['Post']['items'];
				$action = $this->data['Post']['action'];
				
				foreach ($items as $post_id) {
					if (is_numeric($post_id)) {
						if ($action == 'delete') {
							$this->Topic->Post->destroy($post_id);
							$this->Session->setFlash(sprintf(__d('forum', 'A total of %d post(s) have been permanently deleted', true), count($items)));
						}
					}
				}
			}
		}
		
		// Paginate
		$this->paginate['Post']['limit'] = $this->Toolbar->settings['posts_per_page'];
		$this->paginate['Post']['conditions']['Post.topic_id'] = $id;
		
		$this->Toolbar->pageTitle(__d('forum', 'Moderate', true), $topic['Topic']['title']);
		$this->set('id', $id);
		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
	}
	
	/**
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'view', 'feed');
		$this->Security->disabledFields = array('option', 'items');
		$this->set('menuTab', '');
	}

}
