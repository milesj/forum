<?php
/** 
 * Cupcake - Toolbar Component
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/forum-plugin
 */
 
class ToolbarComponent extends Object {

	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('Session');
	
	/**
	 * Initialize.
	 *
	 * @access public
	 * @param obj $Controller
	 * @param array $settings 
	 * @return void
	 */  
	public function initialize(&$Controller, $settings = array()) {
		$this->Controller = $Controller;
		$this->settings = ForumConfig::getInstance()->settings;
		$this->columnMap = ForumConfig::getInstance()->columnMap;
	}

	/**
	 * Initialize the session and all data.
	 *
	 * @access public
	 * @return void
	 */
	public function initForum() {
		if (!$this->Session->check('Forum.isBrowsing')) {
			$user_id = $this->Controller->Auth->user('id');

			// How much access we have?
			if (!$this->Session->check('Forum.access')) {
				$access = array('Guest' => 0);

				if ($user_id) {
					$access['Member'] = 1;
					$access = array_merge($access, ClassRegistry::init('Forum.Access')->getMyAccess($user_id));
				}

				$this->Session->write('Forum.access', $access);
			}

			// Save last visit time
			if (!$this->Session->check('Forum.lastVisit')) {
				$lastVisit = ($user_id) ? $this->Controller->Auth->user($this->columnMap['lastLogin']) : date('Y-m-d H:i:s');
				$this->Session->write('Forum.lastVisit', $lastVisit);
			}

			// Moderator?
			if (!$this->Session->check('Forum.moderates')) {
				$moderates = ($user_id) ? ClassRegistry::init('Forum.Moderator')->getModerations($user_id) : array();
				$this->Session->write('Forum.moderates', $moderates);
			}

			// Are we a super mod?
			if (!$this->Session->check('Forum.isSuperMod')) {
				$status = ($user_id) ? ClassRegistry::init('Forum.Access')->isSuper($user_id) : 0;
				$this->Session->write('Forum.isSuperMod', $status);
			}

			// Are we an administrator?
			if (!$this->Session->check('Forum.isAdmin')) {
				$status = ($user_id) ? ClassRegistry::init('Forum.Access')->isAdmin($user_id) : 0;
				$this->Session->write('Forum.isAdmin', $status);
			}

			$this->Session->write('Forum.isBrowsing', true);
		}
	}
	
	/**
	 * Calculates the page to redirect to.
	 *
	 * @access public
	 * @param int $topic_id
	 * @param int $post_id
	 * @param boolean $return
	 * @return mixed
	 */
	public function goToPage($topic_id = NULL, $post_id = NULL, $return = false) {
		$topic = ClassRegistry::init('Forum.Topic')->find('first', array(
			'conditions' => array('Topic.id' => $topic_id),
			'fields' => array('Topic.slug')
		));

		$slug = (!empty($topic['Topic']['slug']) ? $topic['Topic']['slug'] : null);

		// Certain page
		if ($topic_id && $post_id) {
			$posts = ClassRegistry::init('Forum.Post')->find('list', array(
				'conditions' => array('Post.topic_id' => $topic_id),
				'order' => array('Post.id' => 'ASC')
			));
			
			$totalPosts = count($posts);
			$perPage = $this->settings['posts_per_page'];
			
			if ($totalPosts > $perPage) {
				$totalPages = ceil($totalPosts / $perPage);
			} else {
				$totalPages = 1;
			}
			
			if ($totalPages <= 1) {
				$url = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug, '#' => 'post_'. $post_id);
			} else {
				$posts = array_values($posts);
				$flips = array_flip($posts);
				$position = $flips[$post_id] + 1;
				$goTo = ceil($position / $perPage);
				$url = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug, 'page' => $goTo, '#' => 'post_'. $post_id);
			}
			
		// First post
		} else if ($topic_id && !$post_id) {
			$url = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug);

		// None
		} else {
			$url = $this->Controller->referer();
		
			if ((empty($url)) || (strpos($url, 'delete') !== false)) {
				$url = array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index');
			}
		}
		
		if ($return === true) {
			return $url;
		} else {
			$this->Controller->redirect($url);
		}
	}
	
	/**
	 * Gets the highest access level.
	 *
	 * @access public
	 * @return int
	 */
	public function getAccess() {
		$access = $this->Session->read('Forum.access');
		$level = 0;
		 
		if (!empty($access)) {
			foreach ($access as $no) {
				if ($no > $level) {
					$level = $no;
				}
			}
		}
		
		return $level;
	}
	
	/**
	 * Simply marks a topic as read.
	 *
	 * @access public
	 * @param int $topic_id
	 * @return void
	 */
	public function markAsRead($topic_id) {
		$readTopics = $this->Session->read('Forum.readTopics');
		
		if (is_array($readTopics) && !empty($readTopics)) {
			$readTopics[] = $topic_id;
			$readTopics = array_unique($readTopics);
			$this->Session->write('Forum.readTopics', $readTopics);
		} else {
			$this->Session->write('Forum.readTopics', array($topic_id));
		}
		
		return true;
	}
	
	/**
	 * Builds the page title.
	 *
	 * @access public
	 * @param array $args
	 * @return string
	 */
	public function pageTitle() {
		$args = func_get_args();
		array_unshift($args, __d('forum', 'Forum', true));
		
		$this->Controller->set('title_for_layout', implode(' &raquo; ', $args));
	}
	
	/**
	 * Method for reseting a password.
	 *
	 * @access public
	 * @param array $user
	 * @param boolean $reset
	 * @return void
	 */
	public function resetPassword($user, $reset = false) {
		$User = ClassRegistry::init('Forum.User');
		$password = $User->generate();
		$User->resetPassword($user['User']['id'], $this->Controller->Auth->password($password));
		
		// Send email
		if (!$reset) {
			$message = sprintf(__d('forum', 'You have requested the login credentials for %s, your information is listed below', true), $this->settings['site_name']) .":\n\n";
			$subject = __d('forum', 'Forgotten Password', true);
		} else {
			$message = sprintf(__d('forum', 'Your password has been reset for %s, your information is listed below', true), $this->settings['site_name']) .":\n\n";
			$subject = __d('forum', 'Reset Password', true);
		}
		
		$message .= __d('forum', 'Username', true) .": ". $user['User']['username'] ."\n";
		$message .= __d('forum', 'Password', true) .": ". $password ."\n\n";
		$message .= __d('forum', 'Please change your password once logging in.', true);
		
		$this->Controller->Email->to = $user['User']['username'] .' <'. $user['User']['email'] .'>';
		$this->Controller->Email->from = $this->settings['site_name'] .' <'. $this->settings['site_email'] .'>';
		$this->Controller->Email->subject = $this->settings['site_name'] .' - '. $subject;
		$this->Controller->Email->send($message);
	}
	
	/**
	 * Updates the session topics array.
	 *
	 * @access public
	 * @param int $topic_id
	 * @return void
	 */
	public function updateTopics($topic_id) {
		$topics = $this->Session->read('Forum.topics');
		
		if (!empty($topic_id)) {
			if (is_array($topics)) {
				$topics[$topic_id] = time();
			} else {
				$topics = array($topic_id => time());
			}
			
			$this->Session->write('Forum.topics', $topics);
		}
	}
	
	/**
	 * Updates the session posts array.
	 *
	 * @access public
	 * @param int $post_id
	 * @return void
	 */
	public function updatePosts($post_id) {
		$posts = $this->Session->read('Forum.posts');
		
		if (!empty($post_id)) {
			if (is_array($posts)) {
				$posts[$post_id] = time();
			} else {
				$posts = array($post_id => time());
			}
			
			$this->Session->write('Forum.posts', $posts);
		}
	}
	
	/**
	 * Do we have access to commit this action.
	 *
	 * @access public
	 * @param array $validators
	 * @return boolean
	 */
	public function verifyAccess($validators = array()) {
		$user_id = $this->Controller->Auth->user('id');
		
		// Does the data exist?
		if (isset($validators['exists'])) {
			if (empty($validators['exists'])) {
				$this->goToPage();
			}
		}
		
		// Are we a moderator? Grant access
		if (isset($validators['moderate'])) {
			if (in_array($validators['moderate'], $this->Session->read('Forum.moderates'))) {
				return true;
			}
		}
		
		// Do we have permission to do this action?
		if (isset($validators['permission'])) {
			if ($this->getAccess() < $validators['permission']) {
				$this->goToPage();
			}
		}
		
		// Is the item locked/unavailable?
		if (isset($validators['status'])) {
			if ($validators['status'] > 0) {
				$this->goToPage();
			}
		}
		
		// Does the user own this item?
		if (isset($validators['ownership'])) {
			if (($this->Session->read('Forum.isSuperMod') >= 1) || ($this->Session->read('Forum.isAdmin') >= 1)) {
				return true;
			} else if ($user_id != $validators['ownership']) {
				$this->goToPage();
			}
		}
		
		return true;
	}
	
	/**
	 * Double check access levels in session and db and permit.
	 * 
	 * @access public
	 * @return boolean
	 */
	public function verifyAdmin() {
		$user_id = $this->Controller->Auth->user('id');
		
		if ($user_id) {
			if ($this->Session->read('Forum.isAdmin') >= 1) {
				return true;
			} else {
				$this->goToPage();
			}
		} else {
			$this->Controller->redirect(array('plugin' => 'forum', 'controller' => 'users', 'action' => 'login', 'admin' => false));
		}
		
		return false;
	}

}
