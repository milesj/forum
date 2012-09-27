<?php
/**
 * Forum - PostsController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

class PostsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Post', 'Forum.Profile');

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
			'permission' => $topic['Forum']['accessReply']
		));

		if ($this->request->data) {
			$this->request->data['Post']['forum_id'] = $topic['Topic']['forum_id'];
			$this->request->data['Post']['topic_id'] = $topic['Topic']['id'];
			$this->request->data['Post']['user_id'] = $user_id;
			$this->request->data['Post']['userIP'] = $this->request->clientIp();

			if ($post_id = $this->Post->add($this->request->data['Post'])) {
				if ($topic['Forum']['settingPostCount']) {
					$this->Profile->increasePosts($user_id);
				}

				$this->ForumToolbar->updatePosts($post_id);
				$this->ForumToolbar->goToPage($topic['Topic']['id'], $post_id);
			}
		} else {
			if ($quote_id) {
				if ($quote = $this->Post->getQuote($quote_id)) {
					$this->request->data['Post']['content'] = '[quote="' . $quote['User'][$this->config['userMap']['username']] . '" date="' . $quote['Post']['created'] . '"]' . $quote['Post']['content'] . '[/quote]';
				}
			}
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Post Reply'), $topic['Topic']['title']);
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
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $post,
			'moderate' => $post['Topic']['forum_id'],
			'ownership' => $post['Post']['user_id']
		));

		if ($this->request->data) {
			$this->Post->id = $id;

			if ($this->Post->save($this->request->data, true, array('content', 'contentHtml'))) {
				$this->ForumToolbar->goToPage($post['Post']['topic_id'], $id);
			}
		} else {
			$this->request->data = $post;
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Edit Post'));
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
		$this->loadModel('Forum.Report');

		$post = $this->Post->getById($id);
		$user_id = $this->Auth->user('id');

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $post
		));

		if ($this->request->data) {
			$this->request->data['Report']['user_id'] = $user_id;
			$this->request->data['Report']['item_id'] = $id;
			$this->request->data['Report']['itemType'] = Report::POST;

			if ($this->Report->save($this->request->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have successfully reported this post! A moderator will review this post and take the necessary action.'));
				unset($this->request->data['Report']);
			}
		} else {
			$this->request->data['Report']['post'] = $post['Post']['content'];
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Report Post'));
		$this->set('post', $post);
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index');

		$this->set('menuTab', 'forums');
	}

}
