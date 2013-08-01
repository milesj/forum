<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

/**
 * @property Topic $Topic
 * @property ForumUser $ForumUser
 */
class ForumController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @type array
	 */
	public $uses = array('Forum.Topic', 'Forum.ForumUser');

	/**
	 * Components.
	 *
	 * @type array
	 */
	public $components = array('RequestHandler');

	/**
	 * Helpers.
	 *
	 * @type array
	 */
	public $helpers = array('Rss');

	/**
	 * Forum index.
	 */
	public function index() {
		if ($this->RequestHandler->isRss()) {
			$this->set('items', $this->Topic->getLatest());
			return;
		}

		$this->set('menuTab', 'forums');
		$this->set('forums', 		$this->Topic->Forum->getIndex());
		$this->set('totalPosts', 	$this->Topic->Post->getTotal());
		$this->set('totalTopics', 	$this->Topic->getTotal());
		$this->set('totalUsers', 	$this->ForumUser->getTotal());
		$this->set('newestUser', 	$this->ForumUser->getNewestUser());
		$this->set('whosOnline', 	$this->ForumUser->whosOnline());
	}

	/**
	 * Help.
	 */
	public function help() {
		$this->set('menuTab', 'help');
	}

	/**
	 * Rules.
	 */
	public function rules() {
		$this->set('menuTab', 'rules');
	}

	/**
	 * Jump to a specific topic and post.
	 *
	 * @param int $topic_id
	 * @param int $post_id
	 */
	public function jump($topic_id, $post_id = null) {
		$this->ForumToolbar->goToPage($topic_id, $post_id);
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow();
	}

}
