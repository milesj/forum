<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

/**
 * @property Topic $Topic
 * @property PostRating $PostRating
 * @property Subscription $Subscription
 * @property AjaxHandlerComponent $AjaxHandler
 */
class TopicsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @type array
	 */
	public $uses = array('Forum.Topic', 'Forum.Subscription');

	/**
	 * Components.
	 *
	 * @type array
	 */
	public $components = array('Utility.AjaxHandler', 'RequestHandler');

	/**
	 * Pagination.
	 *
	 * @type array
	 */
	public $paginate = array(
		'Post' => array(
			'order' => array('Post.created' => 'ASC'),
			'contain' => array('User')
		)
	);

	/**
	 * Helpers.
	 *
	 * @type array
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
			'access' => $forum['Forum'][$access]
		));

		if ($this->request->data) {
			$this->request->data['Topic']['status'] = Topic::OPEN;
			$this->request->data['Topic']['user_id'] = $user_id;
			$this->request->data['Topic']['userIP'] = $this->request->clientIp();

			if ($topic_id = $this->Topic->addTopic($this->request->data['Topic'])) {
				$this->ForumToolbar->updateTopics($topic_id);
				$this->ForumToolbar->goToPage($topic_id);
			}
		} else {
			$this->request->data['Topic']['forum_id'] = $forum['Forum']['id'];
		}

		$this->set('pageTitle', $pageTitle);
		$this->set('type', $type);
		$this->set('forum', $forum);
		$this->set('forums', $this->Topic->Forum->getHierarchy());
	}

	/**
	 * Edit a topic.
	 *
	 * @param string $slug
	 * @param string $type
	 */
	public function edit($slug, $type = '') {
		$topic = $this->Topic->getBySlug($slug);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
			'moderate' => $topic['Topic']['forum_id'],
			'ownership' => $topic['Topic']['user_id']
		));

		if ($this->request->data) {
			if ($this->Topic->saveAll($this->request->data, array('validate' => 'only'))) {
				if ($this->Topic->editTopic($topic['Topic']['id'], $this->request->data)) {
					$this->ForumToolbar->goToPage($topic['Topic']['id']);
				}
			}
		} else {
			if ($topic['Poll']['expires']) {
				$topic['Poll']['expires'] = $this->Topic->daysBetween($topic['Poll']['created'], $topic['Poll']['expires']);
			}

			$this->request->data = $topic;
		}

		$this->set('topic', $topic);
		$this->set('forums', $this->Topic->Forum->getHierarchy());
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
		$topic = $this->Topic->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic
		));

		if ($this->request->is('post')) {
			$data = $this->request->data['Report'];

			if ($this->AdminToolbar->reportItem($data['type'], $this->Topic, $topic['Topic']['id'], $data['comment'], $user_id)) {
				$this->Session->setFlash(__d('forum', 'You have successfully reported this topic! A moderator will review this topic and take the necessary action.'));
				unset($this->request->data['Report']);
			}
		}

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
			'status' => $topic['Forum']['status'],
			'access' => $topic['Forum']['accessRead']
		));

		$this->paginate['Post']['limit'] = $this->settings['postsPerPage'];
		$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);

		if ($this->RequestHandler->isRss()) {
			$this->set('posts', $this->paginate('Post'));
			$this->set('topic', $topic);

			return;
		}

		$this->loadModel('Forum.PostRating');

		if (!empty($this->request->data['Poll']['option'])) {
			$this->Topic->Poll->vote($topic['Poll']['id'], $this->request->data['Poll']['option'], $user_id);
			$this->Topic->deleteCache(array('Topic::getBySlug', $slug));

			$this->redirect(array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug));
		}

		$this->ForumToolbar->markAsRead($topic['Topic']['id']);
		$this->Topic->increaseViews($topic['Topic']['id']);

		$this->set('topic', $topic);
		$this->set('posts', $this->paginate('Post'));
		$this->set('subscription', $this->Subscription->isSubscribedToTopic($user_id, $topic['Topic']['id']));
		$this->set('ratings', $this->PostRating->getRatingsInTopic($user_id, $topic['Topic']['id']));
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

		if ($this->settings['enableTopicSubscriptions'] && $this->Subscription->subscribeToTopic($this->Auth->user('id'), $id)) {
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

		if ($this->settings['enableTopicSubscriptions'] && $this->Subscription->unsubscribe($id)) {
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

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
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

		$this->paginate['Post']['limit'] = $this->settings['postsPerPage'];
		$this->paginate['Post']['conditions'] = array('Post.topic_id' => $topic['Topic']['id']);

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
		$this->Security->unlockedFields = array('option', 'items');

		$this->set('menuTab', 'forums');
	}

}
