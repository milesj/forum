<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

/**
 * @property Post $Post
 * @property PostRating $PostRating
 * @property AjaxHandlerComponent $AjaxHandler
 */
class PostsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @type array
	 */
	public $uses = array('Forum.Post');

	/**
	 * Components.
	 *
	 * @type array
	 */
	public $components = array('Utility.AjaxHandler');

	/**
	 * Redirect.
	 */
	public function index() {
		$this->ForumToolbar->goToPage();
	}

	/**
	 * Add post / reply to topic.
	 *
	 * @param string $slug
	 * @param int $quote_id
	 */
	public function add($slug, $quote_id = null) {
		$topic = $this->Post->Topic->getBySlug($slug);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $topic,
			'status' => $topic['Topic']['status'],
			'access' => $topic['Forum']['accessReply']
		));

		if ($this->request->data) {
			$this->request->data['Post']['forum_id'] = $topic['Topic']['forum_id'];
			$this->request->data['Post']['topic_id'] = $topic['Topic']['id'];
			$this->request->data['Post']['user_id'] = $user_id;
			$this->request->data['Post']['userIP'] = $this->request->clientIp();

			if ($post_id = $this->Post->addPost($this->request->data['Post'])) {
				$this->ForumToolbar->updatePosts($post_id);
				$this->ForumToolbar->goToPage($topic['Topic']['id'], $post_id);
			}

		} else if ($quote_id) {
			if ($quote = $this->Post->getQuote($quote_id)) {
				$this->request->data['Post']['content'] = sprintf('[quote="%s" date="%s"]%s[/quote]',
					$quote['User'][$this->config['User']['fieldMap']['username']],
					$quote['Post']['created'],
					$quote['Post']['content']
				) . PHP_EOL;
			}
		}

		$this->set('topic', $topic);
		$this->set('review', $this->Post->getTopicReview($topic['Topic']['id']));
	}

	/**
	 * Edit a post.
	 *
	 * @param int $id
	 */
	public function edit($id) {
		$post = $this->Post->getById($id);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $post,
			'moderate' => $post['Topic']['forum_id'],
			'ownership' => $post['Post']['user_id']
		));

		if ($this->request->data) {
			$this->Post->id = $id;

			if ($this->Post->save($this->request->data, true, array('content'))) {
				$this->ForumToolbar->goToPage($post['Post']['topic_id'], $id);
			}
		} else {
			$this->request->data = $post;
		}

		$this->set('post', $post);
	}

	/**
	 * Delete a post.
	 *
	 * @param int $id
	 */
	public function delete($id) {
		$post = $this->Post->getById($id);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $post,
			'moderate' => $post['Topic']['forum_id'],
			'ownership' => $post['Post']['user_id']
		));

		$this->Post->delete($id, true);
		$this->redirect(array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug']));
	}

	/**
	 * Report a post.
	 *
	 * @param int $id
	 */
	public function report($id) {
		$post = $this->Post->getById($id);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $post
		));

		if ($this->request->is('post')) {
			$data = $this->request->data['Report'];

			if ($this->AdminToolbar->reportItem($data['type'], $this->Post, $id, $data['comment'], $user_id)) {
				$this->Session->setFlash(__d('forum', 'You have successfully reported this post! A moderator will review this post and take the necessary action.'));
				unset($this->request->data['Report']);
			}
		}

		$this->request->data['Report']['post'] = $post['Post']['content'];

		$this->set('post', $post);
	}

	/**
	 * Preview the Decoda markup.
	 */
	public function preview() {
		$input = isset($this->request->data['input']) ? $this->request->data['input'] : '';

		$this->set('input', $input);
		$this->layout = false;
	}

	/**
	 * Rate a post up or down.
	 *
	 * @param int $id
	 * @param int $type
	 */
	public function rate($id, $type) {
		$this->loadModel('Forum.PostRating');

		$user_id = $this->Auth->user('id');
		$post = $this->Post->getById($id);
		$success = true;

		if ($type != PostRating::UP && $type != PostRating::DOWN || !$post) {
			$success = false;

		} else if ($this->PostRating->hasRated($user_id, $id)) {
			$success = false;

		} else if (!$this->PostRating->ratePost($user_id, $id, $post['Post']['topic_id'], $type)) {
			$success = false;
		}

		$this->AjaxHandler->respond('json', array(
			'success' => $success
		));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		if ($this->request->is('ajax')) {
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		}

		$this->Auth->allow('index', 'preview');
		$this->AjaxHandler->handle('rate');

		$this->set('menuTab', 'forums');
	}

}
