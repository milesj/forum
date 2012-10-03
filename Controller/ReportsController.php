<?php
/**
 * Forum - ReportsController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppController', 'Forum.Controller');
App::uses('Report', 'Forum.Model');

class ReportsController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Report');

	/**
	 * Pagination.
	 *
	 * @access public
	 * @var array
	 */
	public $paginate = array(
		'Report' => array(
			'order' => array('Report.created' => 'ASC'),
			'limit' => 25,
			'contain' => false
		)
	);

	/**
	 * List of all reports.
	 *
	 * @param int $type
	 */
	public function admin_index($type = 0) {
		if ($type == Report::TOPIC) {
			$this->setAction('admin_topics');

		} else if ($type == Report::POST) {
			$this->setAction('admin_posts');

		} else if ($type == Report::USER) {
			$this->setAction('admin_users');

		} else {
			$this->paginate['Report']['contain'] = array('Reporter' => array('Profile'), 'Topic', 'Post', 'User' => array('Profile'));

			$this->ForumToolbar->pageTitle(__d('forum', 'Reported Items'));
			$this->set('reports', $this->paginate('Report'));
		}
	}

	/**
	 * Reported topics.
	 */
	public function admin_topics() {
		if ($this->request->data) {
			if (!empty($this->request->data['Report']['items'])) {
				$this->loadModel('Forum.Topic');

				foreach ($this->request->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);

					switch ($this->request->data['Report']['action']) {
						case 'delete':
							$this->Topic->delete($item_id, true);
						break;
						case 'close':
							$this->Topic->id = $item_id;
							$this->Topic->saveField('status', Topic::STATUS_CLOSED);
						break;
					}

					$this->Report->delete($report_id, true);
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d topics have been processed'), count($this->request->data['Report']['items'])));
			}
		}

		$this->paginate['Report']['conditions']['Report.itemType'] = Report::TOPIC;
		$this->paginate['Report']['contain']= array('Reporter' => array('Profile'), 'Topic');

		$this->ForumToolbar->pageTitle(__d('forum', 'Reported Topics'));
		$this->set('reports', $this->paginate('Report'));
	}

	/**
	 * Reported posts.
	 */
	public function admin_posts() {
		if ($this->request->data) {
			if (!empty($this->request->data['Report']['items'])) {
				$this->loadModel('Forum.Post');

				foreach ($this->request->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);

					switch ($this->request->data['Report']['action']) {
						case 'delete':
							$this->Post->delete($item_id, true);
						break;
					}

					$this->Report->delete($report_id, true);
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d posts have been processed'), count($this->request->data['Report']['items'])));
			}
		}

		$this->paginate['Report']['conditions']['Report.itemType'] = Report::POST;
		$this->paginate['Report']['contain'] = array('Reporter' => array('Profile'), 'Post' => array('Topic'));

		$this->ForumToolbar->pageTitle(__d('forum', 'Reported Posts'));
		$this->set('reports', $this->paginate('Report'));
	}

	/**
	 * Reported users.
	 */
	public function admin_users() {
		if ($this->request->data) {
			if (!empty($this->request->data['Report']['items'])) {
				$this->loadModel('User');

				foreach ($this->request->data['Report']['items'] as $item) {
					list($report_id, $item_id) = explode(':', $item);

					switch ($this->request->data['Report']['action']) {
						case 'ban':
							$this->User->id = $item_id;
							$this->User->saveField($this->config['userMap']['status'], $this->config['statusMap']['banned']);
						break;
					}

					$this->Report->delete($report_id, true);
				}

				$this->Session->setFlash(sprintf(__d('forum', 'A total of %d users have been processed'), count($this->request->data['Report']['items'])));
			}
		}

		$this->paginate['Report']['conditions']['Report.itemType'] = Report::USER;
		$this->paginate['Report']['contain']= array('Reporter' => array('Profile'), 'User' => array('Profile'));

		$this->ForumToolbar->pageTitle(__d('forum', 'Reported Users'));
		$this->set('reports', $this->paginate('Report'));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Security->disabledFields = array('items');

		$this->set('menuTab', 'reports');
	}

}
