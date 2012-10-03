<?php
/**
 * Forum - SearchController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');

class SearchController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Topic');

	/**
	 * Pagination.
	 *
	 * @access public
	 * @var array
	 */
	public $paginate = array(
		'Topic' => array(
			'order' => array('LastPost.created' => 'DESC'),
			'contain' => array('Forum', 'User', 'Poll', 'LastPost', 'LastUser')
		)
	);

	/**
	 * Search the topics.
	 *
	 * @param string $type
	 */
	public function index($type = '') {
		$searching = false;
		$forums = $this->Topic->Forum->getGroupedHierarchy('accessRead');
		$orderBy = array(
			'LastPost.created' => __d('forum', 'Last post time'),
			'Topic.created' => __d('forum', 'Topic created time'),
			'Topic.post_count' => __d('forum', 'Total posts'),
			'Topic.view_count' => __d('forum', 'Total views')
		);

		if ($this->request->params['named']) {
			foreach ($this->request->params['named'] as $field => $value) {
				$this->request->data['Topic'][$field] = urldecode($value);
			}
		}

		if ($type === 'new_posts') {
			$this->request->data['Topic']['orderBy'] = 'LastPost.created';
			$this->paginate['Topic']['conditions']['LastPost.created >='] = $this->Session->read('Forum.lastVisit');
		}

		if ($this->request->data) {
			$searching = true;

			if (!empty($this->request->data['Topic']['keywords'])) {
				$this->paginate['Topic']['conditions']['Topic.title LIKE'] = '%' . Sanitize::clean($this->request->data['Topic']['keywords']) . '%';
			}

			if (!empty($this->request->data['Topic']['forum_id'])) {
				$this->paginate['Topic']['conditions']['Topic.forum_id'] = $this->request->data['Topic']['forum_id'];
			}

			if (!empty($this->request->data['Topic']['byUser'])) {
				$this->paginate['Topic']['conditions']['User.' . $this->config['userMap']['username'] . ' LIKE'] = '%' . Sanitize::clean($this->request->data['Topic']['byUser']) . '%';
			}

			if (empty($this->request->data['Topic']['orderBy']) || !isset($orderBy[$this->request->data['Topic']['orderBy']])) {
				$this->request->data['Topic']['orderBy'] = 'LastPost.created';
			}

			$this->paginate['Topic']['conditions']['Forum.accessRead <='] = $this->Session->read('Forum.access');
			$this->paginate['Topic']['order'] = array($this->request->data['Topic']['orderBy'] => 'DESC');
			$this->paginate['Topic']['limit'] = $this->settings['topics_per_page'];

			$this->set('topics', $this->paginate('Topic'));
		}

		$this->ForumToolbar->pageTitle(__d('forum', 'Search'));
		$this->set('menuTab', 'search');
		$this->set('searching', $searching);
		$this->set('orderBy', $orderBy);
		$this->set('forums', $forums);
	}

	/**
	 * Proxy action to build named parameters.
	 */
	public function proxy() {
		$named = array();

		if (isset($this->request->data['Search'])) {
			$this->request->data['Topic'] = $this->request->data['Search'];
		}

		foreach ($this->request->data['Topic'] as $field => $value) {
			if ($value !== '') {
				$named[$field] = urlencode($value);
			}
		}

		$this->redirect(array_merge(array('controller' => 'search', 'action' => 'index'), $named));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow('*');
	}

}
