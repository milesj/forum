<?php
/** 
 * posts_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Posts Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum 
 */
 
class PostsController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public
	 * @var string
	 */
	public $name = 'Posts';

	/**
	 * Models
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Post'); 
	
	/**
	 * Redirect
	 * @access public  
	 */ 
	public function index() {
		$this->Toolbar->goToPage(); 
	}
	
	/**
	 * Add post / reply to topic
	 * @access public
	 * @param int $topic_id
	 * @param int $quote_id
	 */
	public function add($topic_id, $quote_id = null) {
		$topic = $this->Post->Topic->getTopicForReply($topic_id);
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $topic,
			'status' => $topic['Topic']['status'], 
			'permission' => $topic['ForumCategory']['accessReply']
		));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->data['Post']['topic_id'] = $topic_id;
			$this->data['Post']['user_id'] = $user_id;
			$this->data['Post']['userIP'] = $this->RequestHandler->getClientIp();

			if ($post_id = $this->Post->addPost($this->data, $this->Toolbar->settings, $this->Session->read('Forum.posts'))) {
				if ($topic['ForumCategory']['settingPostCount'] == 1) {
					$this->Post->User->increasePosts($user_id);
				}
				
				$this->Toolbar->updatePosts($post_id);
				$this->Toolbar->goToPage($topic_id, $post_id);
			}
		}
		
		// Quoteing
		if (!empty($quote_id) && empty($this->data)) {
			$quote = $this->Post->getQuote($quote_id);
			
			if (!empty($quote)) {
				$this->data['Post']['content'] = '[quote="'. $quote['User']['username'] .'"]'. $quote['Post']['content'] .'[/quote]';
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Post Reply', true));
		$this->set('id', $topic_id);
		$this->set('quote_id', $quote_id);
		$this->set('topic', $topic);
		$this->set('review', $this->Post->getTopicReview($topic_id));
	}
	
	/**
	 * Edit a post
	 * @access public
	 * @param int $id
	 */
	public function edit($id) {
		$post = $this->Post->getPostForEdit($id);
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $post, 
			'moderate' => $post['Topic']['forum_category_id'],
			'ownership' => $post['Post']['user_id']
		));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->Post->id = $id;
			
			if ($this->Post->save($this->data, true, array('content'))) {
				$this->Toolbar->goToPage($post['Post']['topic_id'], $id);
			}
		} else {
			$this->data = $post;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Post', true));
		$this->set('id', $id);
		$this->set('post', $post);
	}
	
	/**
	 * Delete a post
	 * @access public
	 * @param int $id
	 */
	public function delete($id) {
		$post = $this->Post->get($id, array('id', 'user_id'), array('Topic.forum_category_id'));
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array(
			'exists' => $post, 
			'moderate' => $post['Topic']['forum_category_id'],
			'ownership' => $post['Post']['user_id']
		));
		
		// Delete All
		$this->Post->destroy($id, $post);
		$this->redirect(array('controller' => 'topics', 'action' => 'view', $post['Post']['topic_id']));
	}
	
	/**
	 * Report a post
	 * @access public
	 * @param int $id
	 */
	public function report($id) {
		$this->loadModel('Forum.Report');
		
		$post = $this->Post->get($id, array('content'), array('Topic.id', 'Topic.title'));
		$user_id = $this->Auth->user('id');
		
		// Access
		$this->Toolbar->verifyAccess(array('exists' => $post));
		
		// Submit Report
		if (!empty($this->data)) {
			$this->data['Report']['user_id'] = $user_id;
			$this->data['Report']['item_id'] = $id;
			$this->data['Report']['itemType'] = 'post';
			
			if ($this->Report->save($this->data, true, array('item_id', 'itemType', 'user_id', 'comment'))) {
				$this->Session->setFlash(__d('forum', 'You have succesfully reported this post! A moderator will review this post and take the necessary action.', true));
				unset($this->data['Report']);
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Report Post', true));
		$this->set('id', $id);
		$this->set('post', $post);
	}
	
	/**
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('index');
		$this->set('menuTab', '');
	}

}
