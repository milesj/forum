<?php
/** 
 * Forum - StationsController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */
 
class StationsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Forum', 'Forum.Subscription');
	
	/**
	 * Components.
	 * 
	 * @access public
	 * @var array
	 */
	public $components = array('Forum.AjaxHandler');  
	
	/**
	 * Pagination.
	 *
	 * @access public
	 * @var array 
	 */
	public $paginate = array(  
		'Topic' => array(
			'order' => array('LastPost.created' => 'DESC'),
			'contain' => array(
				'User', 'LastPost', 'LastUser', 
				'Poll.id', 
				'Forum.id', 'Forum.settingPostCount', 'Forum.settingAutoLock'
			)
		)
	);
	
	/**
	 * Redirect.
	 */
	public function index() {
		$this->ForumToolbar->goToPage(); 
	}

	/**
	 * Read a forum.
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		$forum = $this->Forum->get($slug);
		$user_id = $this->Auth->user('id');
		
		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum, 
			'permission' => $forum['Forum']['accessRead']
		));
		
		$this->paginate['Topic']['limit'] = $this->settings['topics_per_page'];
		$this->paginate['Topic']['conditions'] = array(
			'Topic.forum_id' => $forum['Forum']['id'],
			'Topic.type' => Topic::NORMAL
		);
		
		$this->ForumToolbar->pageTitle($forum['Forum']['title']);
		$this->set('forum', $forum);
		$this->set('topics', $this->paginate('Topic'));
		$this->set('stickies', $this->Forum->Topic->getStickiesInForum($forum['Forum']['id']));
		$this->set('subscription', $this->Subscription->isSubscribedToForum($user_id, $forum['Forum']['id']));
		$this->set('rss', $slug);
	}

	/**
	 * Moderate a forum.
	 *
	 * @param string $slug
	 */
	public function moderate($slug) {
		$forum = $this->Forum->get($slug);
		
		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum, 
			'permission' => $forum['Forum']['accessRead'],
			'moderate' => $forum['Forum']['id']
		));
		
		if (!empty($this->request->data['Topic']['items'])) {
			$items = $this->request->data['Topic']['items'];
			$action = $this->request->data['Topic']['action'];

			foreach ($items as $topic_id) {
				if (is_numeric($topic_id)) {
					$this->Forum->Topic->id = $topic_id;

					if ($action == 'delete') {
						$this->Forum->Topic->delete($topic_id, true);
						$message = __d('forum', 'A total of %d topic(s) have been permanently deleted');

					} else if ($action == 'close') {
						$this->Forum->Topic->saveField('status', Topic::STATUS_CLOSED);
						$message = __d('forum', 'A total of %d topic(s) have been locked to the public');

					} else if ($action == 'open') {
						$this->Forum->Topic->saveField('status', Topic::STATUS_OPEN);
						$message = __d('forum', 'A total of %d topic(s) have been re-opened');

					} else if ($action == 'move') {
						$this->Forum->Topic->saveField('forum_id', $this->request->data['Topic']['move_id']);
						$message = __d('forum', 'A total of %d topic(s) have been moved to another forum category');
					}
				}
			}
			
			$this->Session->setFlash(sprintf($message, count($items)));
		}
		
		$this->paginate['Topic']['limit'] = $this->settings['topics_per_page'];
		$this->paginate['Topic']['conditions'] = array(
			'Topic.forum_id' => $forum['Forum']['id'],
			'Topic.type' => Topic::NORMAL
		);
		
		$this->ForumToolbar->pageTitle(__d('forum', 'Moderate'), $forum['Forum']['title']);
		$this->set('forum', $forum);
		$this->set('topics', $this->paginate('Topic'));
		$this->set('forums', $this->Forum->getGroupedHierarchy('accessRead'));
	}
	
	/**
	 * RSS Feed.
	 *
	 * @param string $slug
	 */
	public function feed($slug) {
		if ($this->request->is('rss')) {
			$forum = $this->Forum->get($slug);
			
			$this->ForumToolbar->verifyAccess(array(
				'exists' => $forum
			));
		
			$this->paginate['Topic']['limit'] = $this->settings['topics_per_page'];
			$this->paginate['Topic']['conditions'] = array('Topic.forum_id' => $forum['Forum']['id']);
			$this->paginate['Topic']['contain'] = array('User', 'LastPost', 'FirstPost');

			$this->set('topics', $this->paginate('Topic'));
			$this->set('forum', $forum);
			$this->set('document', array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
		} else {
			$this->redirect('/forum/stations/feed/'. $slug .'.rss');
		}
	}
	
	/**
	 * Subscribe to a forum.
	 * 
	 * @param type $id 
	 */
	public function subscribe($id) {
		$success = false;
		$data = __d('forum', 'Failed To Subscribe');
		
		if ($this->settings['enable_forum_subscriptions'] && $this->Subscription->subscribeToForum($this->Auth->user('id'), $id)) {
			$success = true;
			$data = __d('forum', 'Subscribed'); 
		}
		
		$this->AjaxHandler->respond('json', array(
			'success' => $success,
			'data' => $data
		));
	}
	
	/**
	 * Unsubscribe from a forum.
	 * 
	 * @param type $id 
	 */
	public function unsubscribe($id) {
		$success = false;
		$data = __d('forum', 'Failed To Unsubscribe');
		
		if ($this->settings['enable_forum_subscriptions'] && $this->Subscription->unsubscribe($id)) {
			$success = true;
			$data = __d('forum', 'Unsubscribed'); 
		}
		
		$this->AjaxHandler->respond('json', array(
			'success' => $success,
			'data' => $data
		));
	}
	
	/**
	 * Admin index.
	 */
	public function admin_index() {
		if (!empty($this->request->data)) {
			$this->Forum->updateOrder($this->request->data);
			$this->Session->setFlash(__d('forum', 'The order of the forums have been updated!'));
		}
		
		$this->ForumToolbar->pageTitle(__d('forum', 'Manage Forums'));
		$this->set('forums', $this->Forum->getAdminIndex());
	}
	
	/**
	 * Add a forum.
	 */
	public function admin_add() {
		if (!empty($this->request->data)) {
			if (empty($this->request->data['Forum']['forum_id'])) {
				$this->request->data['Forum']['forum_id'] = 0;
			}
			
			if (empty($this->request->data['Forum']['access_level_id'])) {
				$this->request->data['Forum']['access_level_id'] = 0;
			}
			
			if ($this->Forum->save($this->request->data, true)) {
				Cache::delete('Forum.getIndex', 'forum');
				
				$this->Session->setFlash(sprintf(__d('forum', 'The %s forum has been added.'), '<strong>'. $this->request->data['Forum']['title'] .'</strong>'));
				$this->redirect(array('controller' => 'stations', 'action' => 'index', 'admin' => true));
			}
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Add Forum'));
		$this->set('method', 'add');
		$this->set('levels', $this->Forum->AccessLevel->getHigherLevels());
		$this->set('forums', $this->Forum->getHierarchy());
		$this->render('admin_form');
	}
	
	/**
	 * Edit a forum.
	 * 
	 * @param int $id
	 */
	public function admin_edit($id) {
		$forum = $this->Forum->getById($id);
		
		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum
		));
		
		if (!empty($this->request->data)) {
			$this->Forum->id = $id;
			
			if (empty($this->request->data['Forum']['forum_id']) || $this->request->data['Forum']['forum_id'] == $id) {
				$this->request->data['Forum']['forum_id'] = 0;
			}
			
			if (empty($this->request->data['Forum']['access_level_id'])) {
				$this->request->data['Forum']['access_level_id'] = 0;
			}
			
			if ($this->Forum->save($this->request->data, true)) {
				Cache::delete('Forum.getIndex', 'forum');
				Cache::delete('Forum.get-'. $forum['Forum']['slug'], 'forum');
				
				$this->Session->setFlash(sprintf(__d('forum', 'The %s forum has been updated.'), '<strong>'. $forum['Forum']['title'] .'</strong>'));
				$this->redirect(array('controller' => 'stations', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->request->data = $forum;
		}
		
		$this->ForumToolbar->pageTitle(__d('forum', 'Edit Forum'), $forum['Forum']['title']);
		$this->set('method', 'edit');
		$this->set('levels', $this->Forum->AccessLevel->getHigherLevels());
		$this->set('forums', $this->Forum->getHierarchy());
		$this->render('admin_form');
	}
	
	/**
	 * Delete a forum.
	 *
	 * @param int $id
	 */
	public function admin_delete($id) {
		$forum = $this->Forum->getById($id);
		
		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum
		));
		
		if (!empty($this->request->data)) {
			$this->Forum->Topic->moveAll($id, $this->request->data['Forum']['move_topics']);
			$this->Forum->moveAll($id, $this->request->data['Forum']['move_forums']);
			$this->Forum->delete($id, true);
			
			Cache::delete('Forum.getIndex', 'forum');

			$this->Session->setFlash(sprintf(__d('forum', 'The %s forum has been deleted, and all its sub-forums and topics have been moved!'), '<strong>'. $forum['Forum']['title'] .'</strong>'));
			$this->redirect(array('controller' => 'stations', 'action' => 'index', 'admin' => true));
		}
		
		$this->ForumToolbar->pageTitle(__d('forum', 'Delete Forum'), $forum['Forum']['title']);
		$this->set('forum', $forum);
		$this->set('levels', $this->Forum->AccessLevel->getHigherLevels());
		$this->set('topicForums', $this->Forum->getGroupedHierarchy());
		$this->set('subForums', $this->Forum->getGroupedHierarchy());
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'view', 'feed');
		$this->AjaxHandler->handle('subscribe', 'unsubscribe');
		$this->Security->disabledFields = array('items');
		
		$this->set('menuTab', 'forums');
	}

}
