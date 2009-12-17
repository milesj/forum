<?php
/** 
 * categories_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Categories Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class CategoriesController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public
	 * @var string
	 */
	public $name = 'Categories'; 

	/**
	 * Models
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.ForumCategory');  
	
	/**
	 * Pagination 
	 * @access public
	 * @var array 
	 */
	public $paginate = array(  
		'Topic' => array(
			'order' => 'LastPost.created DESC',
			'conditions' => array('Topic.type' => 0),
			'contain' => array('User.id', 'User.username', 'LastPost.created', 'LastUser.username', 'Poll.id')
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
	 * Read a category
	 * @access public
	 * @param int $id
	 */
	public function view($id) {
		$category = $this->ForumCategory->getCategoryForViewing($id, $this->Toolbar->getAccess(), $this->Session->read('Forum.access'));
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $category, 
			'permission' => $category['ForumCategory']['accessRead']
		));
		
		// Paginate
		$this->paginate['Topic']['limit'] = $this->Toolbar->settings['topics_per_page'];
		$this->paginate['Topic']['conditions']['Topic.forum_category_id'] = $id;
		
		$this->Toolbar->pageTitle($category['ForumCategory']['title']);
		$this->set('category', $category);
		$this->set('topics', $this->paginate('Topic'));
		$this->set('stickies', $this->ForumCategory->Topic->getStickiesInForum($id));
	}

	/**
	 * Moderate a category
	 * @access public
	 * @param int $id
	 */
	public function moderate($id) {
		$category = $this->ForumCategory->get($id, null, array('Parent', 'Forum.title'));
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $category, 
			'permission' => $category['ForumCategory']['accessRead'],
			'moderate' => $category['ForumCategory']['id']
		));
		
		// Processing
		if ($this->RequestHandler->isPost()) {
			if (!empty($this->data['Topic']['items'])) {
				$items = $this->data['Topic']['items'];
				$action = $this->data['Topic']['action'];
				
				foreach ($items as $topic_id) {
					if (is_numeric($topic_id)) {
						$this->ForumCategory->Topic->id = $topic_id;
						
						if ($action == 'delete') {
							$this->ForumCategory->Topic->destroy($post_id);
							$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topic(s) have been permanently deleted', true), count($items)));
	
						} else if ($action == 'close') {
							$this->ForumCategory->Topic->saveField('status', 1);
							$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topic(s) have been locked to the public', true), count($items)));
							
						} else if ($action == 'open') {
							$this->ForumCategory->Topic->saveField('status', 0);
							$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topic(s) have been re-opened', true), count($items)));
							
						} else if ($action == 'move') {
							$this->ForumCategory->Topic->saveField('forum_category_id', $this->data['Topic']['move_id']);
							$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topic(s) have been moved to another forum category', true), count($items)));
						}
					}
				}
			}
		}
		
		// Paginate
		$this->paginate['Topic']['limit'] = $this->Toolbar->settings['topics_per_page'];
		$this->paginate['Topic']['conditions'] = array('Topic.forum_category_id' => $id);
		
		$this->Toolbar->pageTitle(__d('forum', 'Moderate', true), $category['ForumCategory']['title']);
		$this->set('category', $category);
		$this->set('topics', $this->paginate('Topic'));
		$this->set('forums', $this->ForumCategory->getHierarchy($this->Toolbar->getAccess(), $this->Session->read('Forum.access'), 'read'));
	}
	
	/**
	 * RSS Feed
	 * @access public
	 * @param int $id
	 */
	public function feed($id) {
		if ($this->RequestHandler->isRss()) {
			$category = $this->ForumCategory->get($id);
			$this->Toolbar->verifyAccess(array('exists' => $category));
		
			$this->paginate['Topic']['limit'] = $this->Toolbar->settings['topics_per_page'];
			$this->paginate['Topic']['conditions'] = array('Topic.forum_category_id' => $id);
			$this->paginate['Topic']['contain'] = array('User.id', 'User.username', 'LastPost.created', 'FirstPost.content');
			
			$this->set('items', $this->paginate('Topic'));
			$this->set('category', $category);
			$this->set('document', array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
		} else {
			$this->redirect('/forum/categories/feed/'. $id .'.rss');
		}
	}
	
	/**
	 * Admin index!
	 * @access public
	 * @category Admin
	 */
	public function admin_index() {
		if (!empty($this->data)) {
			$this->ForumCategory->updateOrder($this->data);
			$this->Session->setFlash(__d('forum', 'The order of the forums have been updated!', true));
		}
		
		$this->pageTitle = __d('forum', 'Manage Forums', true);
		$this->set('forums', $this->ForumCategory->Forum->getAdminIndex());
	}
	
	/**
	 * Add a top level forum
	 * @access public
	 * @category Admin
	 */
	public function admin_add_forum() {
		if (!empty($this->data)) {
			if ($this->ForumCategory->Forum->save($this->data, true, array('title', 'status', 'orderNo', 'accessView', 'access_level_id'))) {
				$this->redirect(array('controller' => 'categories', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->pageTitle = __d('forum', 'Add Forum', true);
		$this->set('method', 'add');
		$this->set('levels', $this->ForumCategory->AccessLevel->getHigherLevels());
		$this->render('admin_form_forum');
	}
	
	/**
	 * Edit top level forum
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_edit_forum($id) {
		$forum = $this->ForumCategory->Forum->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $forum));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->ForumCategory->Forum->id = $id;
			
			if ($this->ForumCategory->Forum->save($this->data, true, array('title', 'status', 'orderNo', 'accessView', 'access_level_id'))) {
				$this->redirect(array('controller' => 'categories', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $forum;
		}
		
		$this->pageTitle = __d('forum', 'Edit Forum', true);
		$this->set('id', $id);
		$this->set('method', 'edit');
		$this->set('levels', $this->ForumCategory->AccessLevel->getHigherLevels());
		$this->render('admin_form_forum');
	}
	
	/**
	 * Delete a forum
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_delete_forum($id) {
		$forum = $this->ForumCategory->Forum->get($id, array('id', 'title'));
		$this->Toolbar->verifyAccess(array('exists' => $forum));
		
		// Form Processing
		if (!empty($this->data['Forum']['forum_id'])) {
			$this->ForumCategory->moveAll($forum['Forum']['id'], $this->data['Forum']['forum_id']);
			$this->ForumCategory->Forum->delete($forum['Forum']['id'], true);

			$this->Session->setFlash(sprintf(__d('forum', 'The forum %s has been deleted, and all its forum categories have been moved!', true), '<strong>'. $forum['Forum']['title'] .'</strong>'));
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'admin' => true));
		}
		
		$this->pageTitle = __d('forum', 'Delete Forum', true);
		$this->set('id', $id);
		$this->set('forum', $forum);
		$this->set('levels', $this->ForumCategory->AccessLevel->getHigherLevels());
		$this->set('forums', $this->ForumCategory->Forum->getList($forum['Forum']['id']));
	}
	
	/**
	 * Add a forum category
	 * @access public
	 * @category Admin
	 */
	public function admin_add_category() {
		if (!empty($this->data)) {
			if (empty($this->data['ForumCategory']['parent_id'])) {
				$this->data['ForumCategory']['parent_id'] = 0;
			}
			
			if ($this->ForumCategory->save($this->data, true, array('title', 'description', 'forum_id', 'parent_id', 'status', 'orderNo', 'accessRead', 'accessPost', 'accessReply', 'accessPoll', 'settingPostCount', 'settingAutoLock', 'access_level_id'))) {
				$this->redirect(array('controller' => 'categories', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->pageTitle = __d('forum', 'Add Forum Category', true);
		$this->set('levels', $this->ForumCategory->AccessLevel->getHigherLevels());
		$this->set('forums', $this->ForumCategory->Forum->getList());
		$this->set('categories', $this->ForumCategory->getParents());
		$this->set('method', 'add');
		$this->render('admin_form_category');
	}
	
	/**
	 * Edit a forum category
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_edit_category($id) {
		$category = $this->ForumCategory->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $category));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->ForumCategory->id = $id;
			
			if (empty($this->data['ForumCategory']['parent_id'])) {
				$this->data['ForumCategory']['parent_id'] = 0;
			}
			
			if ($this->ForumCategory->save($this->data, true, array('title', 'description', 'forum_id', 'parent_id', 'status', 'orderNo', 'accessRead', 'accessPost', 'accessReply', 'accessPoll', 'settingPostCount', 'settingAutoLock', 'access_level_id'))) {
				$this->redirect(array('controller' => 'categories', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $category;
		}
		
		$this->pageTitle = __d('forum', 'Edit Forum Category', true);
		$this->set('id', $id);
		$this->set('levels', $this->ForumCategory->AccessLevel->getHigherLevels());
		$this->set('forums', $this->ForumCategory->Forum->getList());
		$this->set('categories', $this->ForumCategory->getParents($id));
		$this->set('method', 'edit');
		$this->render('admin_form_category');
	}
	
	/**
	 * Delete a category
	 * @access public
	 * @category Admin
	 * @param int $id
	 */
	public function admin_delete_category($id) {
		$category = $this->ForumCategory->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $category));
		
		// Form Processing
		if (!empty($this->data['ForumCategory']['category_id'])) {
			$this->ForumCategory->Topic->moveAll($category['ForumCategory']['id'], $this->data['ForumCategory']['category_id']);
			$this->ForumCategory->moveAll($category['ForumCategory']['id'], 0, true);
			$this->ForumCategory->delete($category['ForumCategory']['id'], true);
			
			$this->Session->setFlash(sprintf(__d('forum', 'The forum category %s has been deleted, and all its sub-forums and topics have been moved!', true), '<strong>'. $category['ForumCategory']['title'] .'</strong>'));
			$this->redirect(array('controller' => 'categories', 'action' => 'index', 'admin' => true));
		}
		
		$this->pageTitle = __d('forum', 'Delete Forum Category', true);
		$this->set('id', $id);
		$this->set('category', $category);
		$this->set('categories', $this->ForumCategory->getHierarchy(0, $this->Session->read('Forum.access'), 'read', $id));
	}
	
	/**
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'view', 'feed');
		$this->Security->disabledFields = array('items');
		
		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
			$this->set('menuTab', 'forums');
		} else {
			$this->set('menuTab', '');
		}
	}

}
