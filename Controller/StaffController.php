<?php
/** 
 * Forum - StaffController
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */
 
class StaffController extends ForumAppController {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Access', 'Forum.Moderator', 'Forum.Forum');
	
	/**
	 * List all staff.
	 */
	public function admin_index() {
		$this->Toolbar->pageTitle(__d('forum', 'Staff'));
		$this->set('levels', $this->Access->AccessLevel->getList());
		$this->set('staff', $this->Access->getList());
		$this->set('mods', $this->Moderator->getList());
	}
	
	/**
	 * Add an access / staff.
	 */
	public function admin_add_access() {
		if (!empty($this->data)) {
			if ($user = $this->Access->add($this->data['Access'])) {
				$this->Session->setFlash(sprintf(__d('forum', 'Access has been granted to %s.'), '<strong>'. $user['User'][$this->config['userMap']['username']] .'</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Add Access'));
		$this->set('method', 'add');
		$this->set('levels', $this->Access->AccessLevel->getHigherLevels());
		$this->render('admin_form_access');
	}
	
	/**
	 * Edit an access / staff.
	 * 
	 * @param int $id
	 */
	public function admin_edit_access($id) {
		$access = $this->Access->get($id);
		
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		if (!empty($this->data)) {
			$this->Access->id = $id;
			
			if ($this->Access->save($this->data, true, array('access_level_id'))) {
				$this->Session->setFlash(sprintf(__d('forum', 'Access for %s has been updated.'), '<strong>'. $access['User'][$this->config['userMap']['username']] .'</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $access;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Access'));
		$this->set('method', 'edit');
		$this->set('levels', $this->Access->AccessLevel->getHigherLevels());
		$this->render('admin_form_access');
	}
	
	/**
	 * Delete an access / staff.
	 * 
	 * @param int $id
	 */
	public function admin_delete_access($id) {
		$access = $this->Access->get($id);
		
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		if (!empty($access)) {
			$this->Access->delete($id, true);
			$this->Session->setFlash(sprintf(__d('forum', 'The access levels for %s have been succesfully removed.'), '<strong>'. $access['User'][$this->config['userMap']['username']] .'</strong>'));
		}
		
		$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
	}
	
	/**
	 * Add an access level.
	 */
	public function admin_add_access_level() {
		if (!empty($this->data)) {
			if ($this->Access->AccessLevel->save($this->data, true, array('level', 'title', 'isSuper', 'isAdmin'))) {
				$this->Session->setFlash(sprintf(__d('forum', 'Access level %s has been added.'), '<strong>'. $this->data['AccessLevel']['title'] .'</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Add Access Level'));
		$this->set('method', 'add');
		$this->render('admin_form_access_level');
	}
	
	/**
	 * Edit an access level.
	 * 
	 * @param $id
	 */
	public function admin_edit_access_level($id) {
		$access = $this->Access->AccessLevel->get($id);
		
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		if (!empty($this->data)) {
			$this->Access->AccessLevel->id = $id;
			
			if ($this->Access->AccessLevel->save($this->data, true, array('level', 'title', 'isSuper', 'isAdmin'))) {
				$this->Session->setFlash(sprintf(__d('forum', 'Access level %s has been updated.'), '<strong>'. $access['AccessLevel']['title'] .'</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $access;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Access Level'));
		$this->set('method', 'edit');
		$this->render('admin_form_access_level');
	}
	
	/**
	 * Delete an access level.
	 * 
	 * @param $id
	 */
	public function admin_delete_access_level($id) {
		$access = $this->Access->AccessLevel->get($id);
		
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		if (!empty($this->data['AccessLevel']['access_level_id'])) {
			$this->Access->moveAll($id, $this->data['AccessLevel']['access_level_id']);
			$this->Access->AccessLevel->delete($id, true);

			$this->Session->setFlash(sprintf(__d('forum', 'The level %s has been deleted, and all its users have been moved!'), '<strong>'. $access['AccessLevel']['title'] .'</strong>'));
			$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Delete Access Level'));
		$this->set('access', $access);
		$this->set('levels', $this->Access->AccessLevel->getHigherLevels($id));
	}
	
	/**
	 * Adds a moderator.
	 */
	public function admin_add_moderator() {
		if (!empty($this->data)) {
			if ($this->Moderator->add($this->data['Moderator'])) {
				$this->Access->grant($this->data['Moderator']['user_id'], Access::MOD);
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Add Moderator'));
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
		$mod = $this->Moderator->get($id);
		
		$this->Toolbar->verifyAccess(array('exists' => $mod));
		
		if (!empty($this->data)) {
			if ($this->Moderator->edit($id, $this->data['Moderator'])) {
				$this->Session->setFlash(sprintf(__d('forum', 'Moderator %s has been updated.'), '<strong>'. $mod['User'][$this->config['userMap']['username']] .'</strong>'));
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $mod;
		}
		
		$this->Toolbar->pageTitle(__d('forum', 'Edit Moderator'));
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
		$mod = $this->Moderator->get($id);
		
		$this->Toolbar->verifyAccess(array('exists' => $mod));
		
		if (!empty($mod)) {
			$this->Moderator->delete($id, true);

			if (!$this->Moderator->getModerations($mod['Moderator']['user_id'])) {
				$this->Access->deleteAll(array(
					'Access.user_id' => $mod['Moderator']['user_id'],
					'Access.access_level_id' => Access::MOD
				));
			}
			
			$this->Session->setFlash(sprintf(__d('forum', 'The moderator %s has been succesfully removed!'), '<strong>'. $mod['User'][$this->config['userMap']['username']] .'</strong>'));
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
