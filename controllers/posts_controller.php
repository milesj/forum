<?php
/** 
 * Forum - Posts Controller
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
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
		$this->Toolbar->goToPage(); 
	}
	
	/**
	 * Add post / reply to topic.
	 *
	 * @param string $slug
	 * @param int $quote_id
	 */
	public function add($slug, $quote_id = null) {
		$topic = $this->Post->Topic->get($slug);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic,
			'status' => $topic['Topic']['status'], 
			'permission' => $topic['Forum']['accessReply']
		));
		
		if (!empty($this->data)) {
			$this->data['Post']['forum_id'] = $topic['Topic']['forum_id'];
			$this->data['Post']['topic_id'] = $topic['Topic']['id'];
			$this->data['Post']['user_id'] = $user_id;
			$this->data['Post']['userIP'] = $this->RequestHandler->getClientIp();

			if ($post_id = $this->Post->add($this->data['Post'])) {
				if ($topic['Forum']['settingPostCount']) {
					$this->Profile->increasePosts($user_id);
				}
				
				$this->Toolbar->updatePosts($post_id);
				$this->Toolbar->goToPage($topic['Topic']['id'], $post_id);
			}
		} else {
			if ($quote_id) {
				$quote = $this->Post->getQuote($quote_id);

				if (!empty($quote)) {
					$this->data['Post']['content'] = '[quote="'. $quote['User'][$this->config['userMap']['username']] .'" date="'. $quote['Post']['created'] .'"]'. $quote['Post']['content'] .'[/quote]';
				}
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Post Reply', true), $topic['Topic']['title']);
		$this->set('topic', $topic);
		$this->set('review', $this->Post->getTopicReview($topic['Topic']['id']));
	}
	
	/**
	 * Edit a post.
	 *
	 * @param int $id
	 */
	public function edit($id) {
		$post = $this->Post->get($id);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $post, 
			'moderate' => $post['Topic']['forum_id'],
			'ownership' => $post['Post']['user_id']
		));
		
		if (!empty($this->data)) {
			$this->Post->id = $id;
			
			if ($this->Post->save($this->data, true, array('content', 'contentHtml'))) {
				$this->Toolbar->goToPage($post['Post']['topic_id'], $id);
			}
		} else {
			$this->data = $post;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Post', true));
		$this->set('post', $post);
	}
	
	/**
	 * Delete a post.
	 *
	 * @param int $id
	 */
	public function delete($id) {
		$post = $this->Post->get($id);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
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
		
		$post = $this->Post->get($id);
		$user_id = $this->Auth->user('id');
		
		$this->Toolbar->verifyAccess(array(
			'exists' => $post
		));
		
		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $user_id;
			$this->data['Report']['item_id'] = $id;
			$this->data['Report']['itemType'] = Report::POST;
			
			if ($this->Report->save($this->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have succesfully reported this post! A moderator will review this post and take the necessary action.', true));
				unset($this->data['Report']);
			}
		} else {
			$this->data['Report']['post'] = $post['Post']['content'];
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Report Post', true));
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
