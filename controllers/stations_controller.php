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
	public $uses = array('Forum.Forum');  
	
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
		$this->Toolbar->goToPage(); 
	}

	/**
	 * Read a forum.
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		$forum = $this->Forum->get($slug);
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $forum, 
			'permission' => $forum['Forum']['accessRead']
		));
		
		$this->paginate['Topic']['limit'] = $this->settings['topics_per_page'];
		$this->paginate['Topic']['conditions'] = array(
			'Topic.forum_id' => $forum['Forum']['id'],
			'Topic.type' => Topic::NORMAL
		);
		
		$this->Toolbar->pageTitle($forum['Forum']['title']);
		$this->set('forum', $forum);
		$this->set('topics', $this->paginate('Topic'));
		$this->set('stickies', $this->Forum->Topic->getStickiesInForum($forum['Forum']['id']));
		$this->set('rss', $slug);
	}

	/**
	 * Moderate a forum.
	 *
	 * @param string $slug
	 */
	public function moderate($slug) {
		$forum = $this->Forum->get($slug);
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $forum, 
			'permission' => $forum['Forum']['accessRead'],
			'moderate' => $forum['Forum']['id']
		));
		
		if (!empty($this->data['Topic']['items'])) {
			$items = $this->data['Topic']['items'];
			$action = $this->data['Topic']['action'];

			foreach ($items as $topic_id) {
				if (is_numeric($topic_id)) {
					$this->Forum->Topic->id = $topic_id;

					if ($action == 'delete') {
						$this->Forum->Topic->delete($topic_id, true);
						$message = __d('forum', 'A total of %d topic(s) have been permanently deleted', true);

					} else if ($action == 'close') {
						$this->Forum->Topic->saveField('status', Topic::STATUS_CLOSED);
						$message = __d('forum', 'A total of %d topic(s) have been locked to the public', true);

					} else if ($action == 'open') {
						$this->Forum->Topic->saveField('status', Topic::STATUS_OPEN);
						$message = __d('forum', 'A total of %d topic(s) have been re-opened', true);

					} else if ($action == 'move') {
						$this->Forum->Topic->saveField('forum_id', $this->data['Topic']['move_id']);
						$message = __d('forum', 'A total of %d topic(s) have been moved to another forum category', true);
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
		
		$this->Toolbar->pageTitle(__d('forum', 'Moderate', true), $forum['Forum']['title']);
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
		if ($this->RequestHandler->isRss()) {
			$forum = $this->Forum->get($slug);
			
			$this->Toolbar->verifyAccess(array(
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
	 * Admin index.
	 */
	public function admin_index() {
		if (!empty($this->data)) {
			$this->Forum->updateOrder($this->data);
			$this->Session->setFlash(__d('forum', 'The order of the forums have been updated!', true));
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Manage Forums', true));
		$this->set('forums', $this->Forum->getAdminIndex());
	}
	
	/**
	 * Add a forum.
	 */
	public function admin_add() {
		if (!empty($this->data)) {
			if (empty($this->data['Forum']['forum_id'])) {
				$this->data['Forum']['forum_id'] = 0;
			}
			
			if (empty($this->data['Forum']['access_level_id'])) {
				$this->data['Forum']['access_level_id'] = 0;
			}
			
			if ($this->Forum->save($this->data, true)) {
				Cache::delete('Forum.getIndex', 'forum');
				
				$this->Session->setFlash(sprintf(__d('forum', 'The %s forum has been added.', true), '<strong>'. $this->data['Forum']['title'] .'</strong>'));
				$this->redirect(array('controller' => 'stations', 'action' => 'index', 'admin' => true));
			}
		}

		$this->Toolbar->pageTitle(__d('forum', 'Add Forum', true));
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
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $forum
		));
		
		if (!empty($this->data)) {
			$this->Forum->id = $id;
			
			if (empty($this->data['Forum']['forum_id']) || $this->data['Forum']['forum_id'] == $id) {
				$this->data['Forum']['forum_id'] = 0;
			}
			
			if (empty($this->data['Forum']['access_level_id'])) {
				$this->data['Forum']['access_level_id'] = 0;
			}
			
			if ($this->Forum->save($this->data, true)) {
				Cache::delete('Forum.getIndex', 'forum');
				Cache::delete('Forum.get-'. $forum['Forum']['slug'], 'forum');
				
				$this->Session->setFlash(sprintf(__d('forum', 'The %s forum has been updated.', true), '<strong>'. $forum['Forum']['title'] .'</strong>'));
				$this->redirect(array('controller' => 'stations', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $forum;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Forum', true), $forum['Forum']['title']);
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
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $forum
		));
		
		if (!empty($this->data)) {
			$this->Forum->Topic->moveAll($id, $this->data['Forum']['move_topics']);
			$this->Forum->moveAll($id, $this->data['Forum']['move_forums']);
			$this->Forum->delete($id, true);
			
			Cache::delete('Forum.getIndex', 'forum');

			$this->Session->setFlash(sprintf(__d('forum', 'The %s forum has been deleted, and all its sub-forums and topics have been moved!', true), '<strong>'. $forum['Forum']['title'] .'</strong>'));
			$this->redirect(array('controller' => 'stations', 'action' => 'index', 'admin' => true));
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Delete Forum', true), $forum['Forum']['title']);
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
		$this->Security->disabledFields = array('items');
		
		$this->set('menuTab', 'forums');
	}

}
