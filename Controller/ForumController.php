<?php
/**
 * Forum - ForumController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

class ForumController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Topic', 'Forum.Profile');

	/**
	 * Forum index.
	 */
	public function index() {
		$this->Toolbar->pageTitle(__d('forum', 'Index'));
		$this->set('menuTab', 'forums');
		$this->set('forums', 		$this->Topic->Forum->getIndex());
		$this->set('totalPosts', 	$this->Topic->Post->getTotal());
		$this->set('totalTopics', 	$this->Topic->getTotal());
		$this->set('totalUsers', 	$this->Profile->getTotal());
		$this->set('newestUser', 	$this->Profile->getNewestUser());
		$this->set('whosOnline', 	$this->Profile->whosOnline());
	}

	/**
	 * RSS Feed.
	 */
	public function feed() {
		if ($this->RequestHandler->isRss()) {
			$this->set('items', $this->Topic->getLatest());
			$this->set('document', array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));
		} else {
			$this->redirect('/forum/feed/feed.rss');
		}
	}

	/**
	 * Help.
	 */
	public function help() {
		$this->Toolbar->pageTitle(__d('forum', 'Help'));
		$this->set('menuTab', 'help');
	}

	/**
	 * Jump to a specific topic and post.
	 *
	 * @param int $topic_id
	 * @param int $post_id
	 */
	public function jump($topic_id, $post_id = null) {
		$this->Toolbar->goToPage($topic_id, $post_id);
	}

	/**
	 * Rules.
	 */
	public function rules() {
		$this->Toolbar->pageTitle(__d('forum', 'Rules'));
		$this->set('menuTab', 'rules');
	}

	/**
	 * Administration home, list statistics.
	 */
	public function admin_index() {
		$this->loadModel('Forum.Report');
		$this->loadModel('Forum.Moderator');
		$this->loadModel('Forum.Profile');

		$this->Toolbar->pageTitle(__d('forum', 'Administration'));
		$this->set('menuTab', 'home');
		$this->set('totalPosts', 	$this->Topic->Post->getTotal());
		$this->set('totalTopics', 	$this->Topic->getTotal());
		$this->set('totalUsers', 	$this->Profile->User->find('count'));
		$this->set('totalProfiles', $this->Profile->getTotal());
		$this->set('totalPolls', 	$this->Topic->Poll->getTotal());
		$this->set('totalReports', 	$this->Report->getTotal());
		$this->set('totalMods', 	$this->Moderator->getTotal());
		$this->set('newestUser', 	$this->Profile->getNewestUser());
		$this->set('latestUsers', 	$this->Profile->getLatest());
		$this->set('latestReports', $this->Report->getLatest());
	}

	/**
	 * Edit the settings.
	 */
	public function admin_settings() {
		$this->loadModel('Forum.Setting');

		if (!empty($this->request->data)) {
			if ($this->Setting->update($this->request->data)) {
				$this->Session->setFlash(__d('forum', 'Settings have been updated!'));

				Cache::delete('Setting.getSettings', 'forum');
				Configure::write('Forum.settings', $this->request->data['Setting']);
			}
		} else {
			$this->request->data['Setting'] = $this->settings;
		}

		$this->Toolbar->pageTitle(__d('forum', 'Settings'));
		$this->set('menuTab', 'settings');
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('index', 'feed', 'help', 'rules', 'jump');
	}

}
