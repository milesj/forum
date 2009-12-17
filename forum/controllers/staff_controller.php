<?php
/** 
 * staff_controller.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Staff Controller
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class StaffController extends ForumAppController {

	/**
	 * Controller Name
	 * @access public
	 * @var string
	 */
	public $name = 'Staff';
	
	/**
	 * Models
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Access', 'Forum.Moderator');
	
	/**
	 * List all staff
	 * @access private
	 * @category Admin
	 */
	public function admin_index() {
		$this->pageTitle = 'Staff';
		$this->set('levels', $this->Access->AccessLevel->getList());
		$this->set('staff', $this->Access->getList());
		$this->set('mods', $this->Moderator->getList());
	}
	
	/**
	 * Add an access / staff
	 * @access private
	 * @category Admin
	 */
	public function admin_add_access() {
		if (!empty($this->data)) {
			if ($this->Access->save($this->data, true, array('user_id', 'access_level_id'))) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->pageTitle = __d('forum', 'Add Access', true);
		$this->set('method', 'add');
		$this->set('levels', $this->Access->AccessLevel->getHigherLevels());
		$this->render('admin_form_access');
	}
	
	/**
	 * Edit an access / staff
	 * @access private
	 * @category Admin
	 * @param int $id
	 */
	public function admin_edit_access($id) {
		$access = $this->Access->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->Access->id = $id;
			
			if ($this->Access->save($this->data, true, array('user_id', 'access_level_id'))) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $access;
		}
		
		$this->pageTitle = __d('forum', 'Edit Access', true);
		$this->set('method', 'edit');
		$this->set('levels', $this->Access->AccessLevel->getHigherLevels());
		$this->render('admin_form_access');
	}
	
	/**
	 * Delete an access / staff
	 * @access private
	 * @category Admin
	 * @param int $id
	 */
	public function admin_delete_access($id) {
		$access = $this->Access->get($id, false, array('User.username'));
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		if (!empty($access)) {
			$this->Access->delete($id, true);
			$this->Session->setFlash(sprintf(__d('forum', 'The access levels for %s have been succesfully removed.', true), '<strong>'. $access['User']['username'] .'</strong>'));
		}
		
		$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
	}
	
	/**
	 * Add an access level
	 * @access private
	 * @category Admin
	 */
	public function admin_add_access_level() {
		if (!empty($this->data)) {
			if ($this->Access->AccessLevel->save($this->data, true, array('level', 'title', 'is_super', 'is_admin'))) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->pageTitle = __d('forum', 'Add Access Level', true);
		$this->set('method', 'add');
		$this->render('admin_form_access_level');
	}
	
	/**
	 * Edit an access level
	 * @access private
	 * @category Admin
	 * @param $id
	 */
	public function admin_edit_access_level($id) {
		$access = $this->Access->AccessLevel->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->Access->AccessLevel->id = $id;
			
			if ($this->Access->AccessLevel->save($this->data, true, array('level', 'title', 'is_super', 'is_admin'))) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $access;
		}
		
		$this->pageTitle = __d('forum', 'Edit Access Level', true);
		$this->set('method', 'edit');
		$this->set('id', $id);
		$this->render('admin_form_access_level');
	}
	
	/**
	 * Delete an access level
	 * @access private
	 * @category Admin
	 * @param $id
	 */
	public function admin_delete_access_level($id) {
		$access = $this->Access->AccessLevel->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $access));
		
		// Form Processing
		if (!empty($this->data['AccessLevel']['access_level_id'])) {
			$this->Access->moveAll($id, $this->data['AccessLevel']['access_level_id']);
			$this->Access->AccessLevel->delete($id, true);

			$this->Session->setFlash(sprintf(__d('forum', 'The level %s has been deleted, and all its users have been moved!', true), '<strong>'. $access['AccessLevel']['title'] .'</strong>'));
			$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
		}
		
		$this->pageTitle = __d('forum', 'Delete Access Level', true);
		$this->set('id', $id);
		$this->set('access', $access);
		$this->set('levels', $this->Access->AccessLevel->getHigherLevels($id));
	}
	
	/**
	 * Adds a moderator
	 * @access private
	 * @category Admin
	 */
	public function admin_add_moderator() {
		if (!empty($this->data)) {
			if ($this->Moderator->save($this->data, true, array('user_id', 'forum_category_id'))) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		}
		
		$this->pageTitle = __d('forum', 'Add Moderator', true);
		$this->set('method', 'add');
		$this->set('forums', $this->Moderator->ForumCategory->getHierarchy(10, $this->Session->read('Forum.access'), 'read'));
		$this->render('admin_form_moderator');
	}
	
	/**
	 * Edit a moderator
	 * @access private
	 * @category Admin
	 * @param $id
	 */
	public function admin_edit_moderator($id) {
		$mod = $this->Moderator->get($id);
		$this->Toolbar->verifyAccess(array('exists' => $mod));
		
		// Form Processing
		if (!empty($this->data)) {
			$this->Moderator->id = $id;
			
			if ($this->Moderator->save($this->data, true, array('user_id', 'forum_category_id'))) {
				$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
			}
		} else {
			$this->data = $mod;
		}
		
		$this->pageTitle = __d('forum', 'Edit Moderator', true);
		$this->set('method', 'edit');
		$this->set('forums', $this->Moderator->ForumCategory->getHierarchy(10, $this->Session->read('Forum.access'), 'read'));
		$this->render('admin_form_moderator');
	}
	
	/**
	 * Delete a moderator
	 * @access private
	 * @category Admin
	 * @param $id
	 */
	public function admin_delete_moderator($id) {
		$mod = $this->Moderator->get($id, false, array('User.username'));
		$this->Toolbar->verifyAccess(array('exists' => $mod));
		
		if (!empty($mod)) {
			$this->Moderator->delete($id, true);
			$this->Session->setFlash(sprintf(__d('forum', 'The moderator %s has been succesfully removed!', true), '<strong>'. $access['AccessLevel']['title'] .'</strong>'));
		}
		
		$this->redirect(array('controller' => 'staff', 'action' => 'index', 'admin' => true));
	}
	
	/**
	 * Before filter
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
			$this->set('menuTab', 'staff');
		}
	}
	
}
