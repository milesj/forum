<?php
/** 
 * Forum - Topics Controller
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class TopicsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Topic', 'Forum.Profile');  
	
	/**
	 * Pagination.
	 *
	 * @access public   
	 * @var array    
	 */ 
	public $paginate = array( 
		'Post' => array(
			'order' => array('Post.created' => 'ASC'),
			'contain' => array(
				'User' => array(
					'Profile', 
					'Access' => array('AccessLevel')
				)
			)
		) 
	);
	
	/**
	 * Helpers.
	 * 
	 * @access public
	 * @var array
	 */
	public $helpers = array('Utils.Gravatar');
	
	/**
	 * Redirect.
	 */
	public function index() {
		$this->Toolbar->goToPage();
	}
	
	/**
	 * Post a new topic or poll.
	 *
	 * @param string $slug
	 * @param string $type
	 */
	public function add($slug, $type = '') {
		$forum = $this->Topic->Forum->get($slug);
		$user_id = $this->Auth->user('id');
		
		if ($type == 'poll') {
			$pageTitle = __d('forum', 'Create Poll', true);
			$access = 'accessPoll';
		} else {
			$pageTitle = __d('forum', 'Create Topic', true);
			$access = 'accessPost';
		}
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $forum, 
			'status' => $forum['Forum']['status'], 
			'permission' => $forum['Forum'][$access]
		));
		
		if (!empty($this->data)) {
			$this->data['Topic']['status'] = 1;
			$this->data['Topic']['user_id'] = $user_id;
			$this->data['Topic']['userIP'] = $this->RequestHandler->getClientIp();

			if ($topic_id = $this->Topic->add($this->data['Topic'])) {
				if ($forum['Forum']['settingPostCount']) {
					$this->Profile->increasePosts($user_id);
					$this->Profile->increaseTopics($user_id);
				}
				
				$this->Toolbar->updateTopics($topic_id);
				$this->Toolbar->goToPage($topic_id);
			}
		} else {
			$this->data['Topic']['forum_id'] = $forum['Forum']['id'];
		}
		
		$this->Toolbar->pageTitle($pageTitle);
		$this->set('pageTitle', $pageTitle);
		$this->set('type', $type);
		$this->set('forum', $forum);
		$this->set('forums', $this->Topic->Forum->getGroupedHierarchy($access));
	}
	
	/**
	 * Edit a topic.
	 *
	 * @param string $slug
	 */
	public function edit($slug) {
		$topic = $this->Topic->get($slug);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'moderate' => $topic['Topic']['forum_id'],
			'ownership' => $topic['Topic']['user_id']
		));
		
		if (!empty($this->data)) {
			if ($this->Topic->saveAll($this->data, array('validate' => 'only'))) {
				if ($this->Topic->edit($topic['Topic']['id'], $this->data)) {
					$this->Toolbar->goToPage($topic['Topic']['id']);
				}
			}
		} else {
			$topic['Poll']['expires'] = $this->Topic->daysBetween($topic['Poll']['created'], $topic['Poll']['expires']);
			$this->data = $topic;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Topic', true));
		$this->set('topic', $topic);
		$this->set('forums', $this->Topic->Forum->getGroupedHierarchy('accessPost'));
	}
	
	/**
	 * RSS Feed.
	 *
	 * @param string $slug
	 */
	public function feed($slug) {
		if ($this->RequestHandler->isRss()) {
			$topic = $this->Topic->get($slug);
			
			$this->Toolbar->verifyAccess(array(
				'exists' => $topic
			));
		
			$this->paginate['Post']['limit'] = $this->settings['posts_per_page'];
			$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);
			$this->paginate['Post']['contain'] = array('User');
			$this->paginate['Post']['order'] = array('Post.created' => 'DESC');

			$this->set('items', $this->paginate('Post'));
			$this->set('topic', $topic);
			$this->set('document', array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
		} else {
			$this->redirect('/forum/topics/feed/'. $slug .'.rss');
		}
	}
	
	/**
	 * Delete a topic.
	 *
	 * @param string $slug
	 */
	public function delete($slug) {
		$topic = $this->Topic->get($slug);
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'moderate' => $topic['Topic']['forum_id']
		));
		
		// @todo - delete cache
		
		$this->Topic->delete($topic['Topic']['id'], true);
		$this->redirect(array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
	}
	
	/**
	 * Report a topic.
	 *
	 * @param string $slug
	 */
	public function report($slug) {
		$this->loadModel('Forum.Report');
		
		$topic = $this->Topic->get($slug);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic
		));
		
		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $user_id;
			$this->data['Report']['item_id'] = $topic['Topic']['id'];
			$this->data['Report']['itemType'] = Report::TOPIC;
			
			if ($this->Report->save($this->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have succesfully reported this topic! A moderator will review this topic and take the necessary action.', true));
				unset($this->data['Report']);
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Report Topic', true));
		$this->set('topic', $topic);
	}

	/**
	 * Read a topic.
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		$topic = $this->Topic->get($slug);
		$user_id = $this->Auth->user('id');

		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'permission' => $topic['Forum']['accessRead']
		));
		
		if (!empty($this->data['Poll']['option'])) {
			$this->Topic->Poll->vote($topic['Poll']['id'], $this->data['Poll']['option'], $user_id);
			$this->redirect(array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug));
		}
		
		$this->Toolbar->markAsRead($topic['Topic']['id']);
		$this->Topic->increaseViews($topic['Topic']['id']);
		
		$this->paginate['Post']['limit'] = $this->settings['posts_per_page'];
		$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);
		
		$this->Toolbar->pageTitle($topic['Forum']['title'], $topic['Topic']['title']);
		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
		$this->set('rss', $slug);
	}
	
	/**
	 * Moderate a topic.
	 *
	 * @param string $slug
	 */
	public function moderate($slug) {
		$topic = $this->Topic->get($slug);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic, 
			'permission' => $topic['Forum']['accessRead'],
			'moderate' => $topic['Topic']['forum_id']
		));
		
		if (!empty($this->data['Post']['items'])) {
			$items = $this->data['Post']['items'];
			$action = $this->data['Post']['action'];

			foreach ($items as $post_id) {
				if (is_numeric($post_id)) {
					if ($action == 'delete') {
						$this->Topic->Post->delete($post_id, true);
						$message = __d('forum', 'A total of %d post(s) have been permanently deleted', true);
					}
				}
			}
			
			$this->Session->setFlash(sprintf($message, count($items)));
		}
		
		$this->paginate['Post']['limit'] = $this->settings['posts_per_page'];
		$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);
		
		$this->Toolbar->pageTitle(__d('forum', 'Moderate', true), $topic['Topic']['title']);
		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
	}
	
	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index', 'view', 'feed');
		$this->Security->disabledFields = array('option', 'items');

		$this->set('menuTab', 'forums');
	}

}
