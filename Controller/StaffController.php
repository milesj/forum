<?php
/**
* @copyright Copyright 2006-2013, Miles Johnson - http://milesj.me
* @license http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
* @link http://milesj.me/code/cakephp/forum
*/

App::uses('ForumAppController', 'Forum.Controller');

/**
* @property Access $Access
* @property Moderator $Moderator
* @property Forum $Forum
*/
class StaffController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @var array
	 */
	public $uses = array('Forum.Access', 'Forum.Moderator', 'Forum.Forum');

	/**
	 * List all staff.
	 */
	public function admin_index() {
		$this->set('staff', $this->Access->getStaff());
		$this->set('mods', $this->Moderator->getList());
	}

	/**
	 * Add an access / staff.
	 */
	public function admin_add_access() {
		if ($this->request->data) {
			if ($user = $this->Access->add($this->request->data['Access'])) {
				$this->Session->setFlash(sprintf(__d('forum', 'Access has been granted to %s.'), '<strong>' . $user['User'][$this->config['userMap']['username']] . '</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}

		$this->set('method', 'add');
		$this->render('admin_form');
	}

	/**
	 * Edit an access / staff.
	 *
	 * @param int $id
	 */
	public function admin_edit_access($id) {
		$access = $this->Access->getById($id);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $access
		));

		if ($this->request->data) {
			$this->Access->id = $id;

			if ($this->Access->save($this->request->data, true, array('parent_id'))) {
				$this->Session->setFlash(sprintf(__d('forum', 'Access for %s has been updated.'), '<strong>' . $access['User'][$this->config['userMap']['username']] . '</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->request->data = $access;
		}

		$this->set('method', 'edit');
		$this->render('admin_form');
	}

	/**
	 * Delete an access / staff.
	 *
	 * @param int $id
	 */
	public function admin_delete_access($id) {
		$access = $this->Access->getById($id);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $access
		));

		if ($access) {
			$this->Access->delete($id, true);
			$this->Session->setFlash(sprintf(__d('forum', 'The access levels for %s have been successfully removed.'), '<strong>' . $access['User'][$this->config['userMap']['username']] . '</strong>'));
		}

		$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
	}

	/**
	 * Adds a moderator.
	 */
	public function admin_add_moderator() {
		if ($this->request->data) {
			if ($this->Moderator->add($this->request->data['Moderator'])) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}

		$this->set('method', 'add');
		$this->set('forums', $this->Forum->getGroupedHierarchy('accessRead'));
		$this->render('admin_form_moderator');
	}

	/**
	 * Edit a moderator.
	 *
	 * @param $id
	 */
	public function admin_edit_moderator($id) {
		$mod = $this->Moderator->getById($id);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $mod
		));

		if ($this->request->data) {
			if ($this->Moderator->edit($id, $this->request->data['Moderator'])) {
				$this->Session->setFlash(sprintf(__d('forum', 'Moderator %s has been updated.'), '<strong>' . $mod['User'][$this->config['userMap']['username']] . '</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->request->data = $mod;
		}

		$this->set('method', 'edit');
		$this->set('forums', $this->Forum->getGroupedHierarchy('accessRead'));
		$this->render('admin_form_moderator');
	}

	/**
	 * Delete a moderator.
	 *
	 * @param $id
	 */
	public function admin_delete_moderator($id) {
		$mod = $this->Moderator->getById($id);

		$this->ForumToolbar->verifyAccess(array(
			'exists' => $mod
		));

		if ($mod) {
			$this->Moderator->delete($id, true);
			$this->Session->setFlash(sprintf(__d('forum', 'The moderator %s has been successfully removed!'), '<strong>' . $mod['User'][$this->config['userMap']['username']] . '</strong>'));
		}

		$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
	}

	/**
	 * Before filter.
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->set('menuTab', 'staff');
	}

}