<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

/**
 * @property Forum $Forum
 * @property Subscription $Subscription
 * @property AjaxHandlerComponent $AjaxHandler
 */
class StationsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @type array
	 */
	public $uses = array('Forum.Forum', 'Forum.Subscription');

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
		'Topic' => array(
			'order' => array('LastPost.created' => 'DESC'),
			'contain' => array(
				'User', 'LastPost', 'LastUser',
				'Poll.id',
				'Forum.id', 'Forum.autoLock'
			)
		)
	);

	/**
	 * Helpers.
	 *
	 * @type array
	 */
	public $helpers = array('Rss', 'Admin.Admin');

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
		$forum = $this->Forum->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum,
			'status' => $forum['Forum']['status'],
			'access' => $forum['Forum']['accessRead']
		));

		$this->paginate['Topic']['limit'] = $this->settings['topicsPerPage'];
		$this->paginate['Topic']['conditions'] = array(
			'Topic.forum_id' => $forum['Forum']['id'],
			'Topic.type' => Topic::NORMAL
		);

		if ($this->RequestHandler->isRss()) {
			$this->paginate['Topic']['contain'] = array('User', 'FirstPost', 'LastPost.created');

			$this->set('topics', $this->paginate('Topic'));
			$this->set('forum', $forum);

			return;
		}

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
		$forum = $this->Forum->getBySlug($slug);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $forum,
			'moderate' => $forum['Forum']['id']
		));

		if (!empty($this->request->data['Topic']['items'])) {
			$items = $this->request->data['Topic']['items'];
			$action = $this->request->data['Topic']['action'];
			$message = null;

			foreach ($items as $topic_id) {
				if (is_numeric($topic_id)) {
					$this->Forum->Topic->id = $topic_id;

					if ($action === 'delete') {
						$this->Forum->Topic->delete($topic_id, true);
						$message = __d('forum', 'A total of %d topic(s) have been permanently deleted');

					} else if ($action === 'close') {
						$this->Forum->Topic->saveField('status', Topic::CLOSED);
						$message = __d('forum', 'A total of %d topic(s) have been locked to the public');

					} else if ($action === 'open') {
						$this->Forum->Topic->saveField('status', Topic::OPEN);
						$message = __d('forum', 'A total of %d topic(s) have been re-opened');

					} else if ($action === 'move') {
						$this->Forum->Topic->saveField('forum_id', $this->request->data['Topic']['move_id']);
						$message = __d('forum', 'A total of %d topic(s) have been moved to another forum category');
					}
				}
			}

			$this->Session->setFlash(sprintf($message, count($items)));
		}

		$this->paginate['Topic']['limit'] = $this->settings['topicsPerPage'];
		$this->paginate['Topic']['conditions'] = array(
			'Topic.forum_id' => $forum['Forum']['id'],
			'Topic.type' => Topic::NORMAL
		);

		$this->set('forum', $forum);
		$this->set('topics', $this->paginate('Topic'));
		$this->set('forums', $this->Forum->getHierarchy());
	}

	/**
	 * Subscribe to a forum.
	 *
	 * @param int $id
	 */
	public function subscribe($id) {
		$success = false;
		$data = __d('forum', 'Failed To Subscribe');

		if ($this->settings['enableForumSubscriptions'] && $this->Subscription->subscribeToForum($this->Auth->user('id'), $id)) {
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
	 * @param int $id
	 */
	public function unsubscribe($id) {
		$success = false;
		$data = __d('forum', 'Failed To Unsubscribe');

		if ($this->settings['enableForumSubscriptions'] && $this->Subscription->unsubscribe($id)) {
			$success = true;
			$data = __d('forum', 'Unsubscribed');
		}

		$this->AjaxHandler->respond('json', array(
			'success' => $success,
			'data' => $data
		));
	}

	/**
	 * Admin override for Forum model delete action.
	 * Provides support for moving topics and forums to a new forum.
	 *
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function admin_delete($id) {
		$this->Model = Admin::introspectModel('Forum.Forum');
		$this->Model->id = $id;

		$result = $this->AdminToolbar->getRecordById($this->Model, $id);

		if (!$result) {
			throw new NotFoundException(__d('admin', '%s Not Found', $this->Model->singularName));
		}

		if ($this->request->is('post')) {
			if ($this->Model->delete($id, true)) {
				$this->Forum->Topic->moveAll($id, $this->request->data['Forum']['move_topics']);
				$this->Forum->moveAll($id, $this->request->data['Forum']['move_forums']);

				$this->AdminToolbar->logAction(ActionLog::DELETE, $this->Model, $id);

				$this->AdminToolbar->setFlashMessage(__d('admin', 'Successfully deleted %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)));
				$this->AdminToolbar->redirectAfter($this->Model);

			} else {
				$this->AdminToolbar->setFlashMessage(__d('admin', 'Failed to delete %s with ID %s', array(mb_strtolower($this->Model->singularName), $id)), 'error');
			}
		}

		// Get tree excluding this record
		$forums = $this->Model->generateTreeList(array('Forum.id !=' => $id), null, null, ' -- ');

		$this->set('result', $result);
		$this->set('moveTopics', $forums);
		$this->set('moveForums', $forums);
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index', 'view', 'feed');
		$this->AjaxHandler->handle('subscribe', 'unsubscribe');
		$this->Security->unlockedFields = array('items');

		$this->set('menuTab', 'forums');
	}

}
