<?php
/**
 * Forum - TopicsController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

class TopicsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @var array
	 */
	public $uses = array('Forum.Topic', 'Forum.Profile', 'Forum.Subscription');

	/**
	 * Components.
	 *
	 * @var array
	 */
	public $components = array('Utility.AjaxHandler', 'RequestHandler');

	/**
	 * Pagination.
	 *
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
	 * @var array
	 */
	public $helpers = array('Rss');

	/**
	 * Redirect.
	 */
	public function index() {
		$this->ForumToolbar->goToPage();
	}

	/**
	 * Post a new topic or poll.
	 *
	 * @param string $slug
	 * @param string $type
	 */
	public function add($slug, $type = '') {
		$forum = $this->Topic->Forum->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		if ($type === 'poll') {
			$pageTitle = __d('forum', 'Create Poll');
			$access = 'accessPoll';
		} else {
			$pageTitle = __d('forum', 'Create Topic');
			$access = 'accessPost';
		}

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum,
			'status' => $forum['Forum']['status'],
			'permission' => $forum['Forum'][$access]
		));

		if ($this->request->data) {
			$this->request->data['Topic']['status'] = Topic::STATUS_OPEN;
			$this->request->data['Topic']['user_id'] = $user_id;
			$this->request->data['Topic']['userIP'] = $this->request->clientIp();

			if ($topic_id = $this->Topic->add($this->request->data['Topic'])) {
				if ($forum['Forum']['settingPostCount']) {
					$this->Profile->increasePosts($user_id);
				}

				$this->Profile->increaseTopics($user_id);
				$this->ForumToolbar->updateTopics($topic_id);
				$this->ForumToolbar->goToPage($topic_id);
			}
		} else {
			$this->request->data['Topic']['forum_id'] = $forum['Forum']['id'];
		}

		$this->ForumToolbar->pageTitle($pageTitle);
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
		$topic = $this->Topic->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
			'moderate' => $topic['Topic']['forum_id'],
			'ownership' => $topic['Topic']['user_id']
		));

		if ($this->request->data) {
			if ($this->Topic->saveAll($this->request->data, array('validate' => 'only'))) {
				if ($this->Topic->edit($topic['Topic']['id'], $this->request->data)) {
					$this->Topic->deleteCache(array('Topic::getBySlug', $slug));
					$this->ForumToolbar->goToPage($topic['Topic']['id']);
				}
			}
		} else {
			$topic['Poll']['expires'] = $this->Topic->daysBetween($topic['Poll']['created'], $topic['Poll']['expires']);
			$this->request->data = $topic;
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Edit Topic'));
		$this->set('topic', $topic);
		$this->set('forums', $this->Topic->Forum->getGroupedHierarchy('accessPost'));
	}

	/**
	 * Delete a topic.
	 *
	 * @param string $slug
	 */
	public function delete($slug) {
		$topic = $this->Topic->getBySlug($slug);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
			'moderate' => $topic['Topic']['forum_id']
		));

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

		$topic = $this->Topic->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic
		));

		if ($this->request->data) {
			$this->request->data['Report']['user_id'] = $user_id;
			$this->request->data['Report']['item_id'] = $topic['Topic']['id'];
			$this->request->data['Report']['itemType'] = Report::TOPIC;

			if ($this->Report->save($this->request->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have successfully reported this topic! A moderator will review this topic and take the necessary action.'));
				unset($this->request->data['Report']);
			}
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Report Topic'));
		$this->set('topic', $topic);
	}

	/**
	 * Read a topic.
	 *
	 * @param string $slug
	 */
	public function view($slug) {
		$topic = $this->Topic->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
			'permission' => $topic['Forum']['accessRead']
		));

		$this->paginate['Post']['limit'] = $this->settings['posts_per_page'];
		$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);

		if ($this->RequestHandler->isRss()) {
			$this->set('posts', $this->paginate('Post'));
			$this->set('topic', $topic);

			return;
		}

		if (!empty($this->request->data['Poll']['option'])) {
			$this->Topic->Poll->vote($topic['Poll']['id'], $this->request->data['Poll']['option'], $user_id);
			$this->Topic->deleteCache(array('Topic::getBySlug', $slug));

			$this->redirect(array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug));
		}

		$this->ForumToolbar->markAsRead($topic['Topic']['id']);
		$this->Topic->increaseViews($topic['Topic']['id']);

		$this->ForumToolbar->pageTitle($topic['Forum']['title'], $topic['Topic']['title']);
		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
		$this->set('subscription', $this->Subscription->isSubscribedToTopic($user_id, $topic['Topic']['id']));
		$this->set('rss', $slug);
	}

	/**
	 * Subscribe to a topic.
	 *
	 * @param int $id
	 */
	public function subscribe($id) {
		$success = false;
		$data = __d('forum', 'Failed To Subscribe');

		if ($this->settings['enable_topic_subscriptions'] && $this->Subscription->subscribeToTopic($this->Auth->user('id'), $id)) {
			$success = true;
			$data = __d('forum', 'Subscribed');
		}

		$this->AjaxHandler->respond('json', array(
			'success' => $success,
			'data' => $data
		));
	}

	/**
	 * Unsubscribe from a topic.
	 *
	 * @param int $id
	 */
	public function unsubscribe($id) {
		$success = false;
		$data = __d('forum', 'Failed To Unsubscribe');

		if ($this->settings['enable_topic_subscriptions'] && $this->Subscription->unsubscribe($id)) {
			$success = true;
			$data = __d('forum', 'Unsubscribed');
		}

		$this->AjaxHandler->respond('json', array(
			'success' => $success,
			'data' => $data
		));
	}

	/**
	 * Moderate a topic.
	 *
	 * @param string $slug
	 */
	public function moderate($slug) {
		$topic = $this->Topic->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
			'permission' => $topic['Forum']['accessRead'],
			'moderate' => $topic['Topic']['forum_id']
		));

		if (!empty($this->request->data['Post']['items'])) {
			$items = $this->request->data['Post']['items'];
			$action = $this->request->data['Post']['action'];
			$message = null;

			foreach ($items as $post_id) {
				if (is_numeric($post_id)) {
					if ($action === 'delete') {
						$this->Topic->Post->delete($post_id, true);
						$message = __d('forum', 'A total of %d post(s) have been permanently deleted');
					}
				}
			}

			$this->Session->setFlash(sprintf($message, count($items)));
		}

		$this->paginate['Post']['limit'] = $this->settings['posts_per_page'];
		$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);

		$this->ForumToolbar->pageTitle(__d('forum', 'Moderate'), $topic['Topic']['title']);
		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index', 'view', 'feed');
		$this->AjaxHandler->handle('subscribe', 'unsubscribe');
		$this->Security->disabledFields = array('option', 'items');

		$this->set('menuTab', 'forums');
	}

}
