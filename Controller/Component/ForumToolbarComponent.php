<?php
/**
 * Forum - ToolbarComponent
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

class ForumToolbarComponent extends Component {

	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('Session');

	/**
	 * Plugin configuration.
	 *
	 * @access public
	 * @var array
	 */
	public $config = array();

	/**
	 * Database forum settings.
	 *
	 * @access public
	 * @var array
	 */
	public $settings = array();

	/**
	 * Controller instance.
	 *
	 * @access public
	 * @var Controller
	 */
	public $Controller;

	/**
	 * Initialize.
	 *
	 * @access public
	 * @param Controller $Controller
	 * @return void
	 */
	public function initialize(Controller $Controller) {
		$this->Controller = $Controller;
		$this->config = Configure::read('Forum');
		$this->settings = Configure::read('Forum.settings');
	}

	/**
	 * Initialize the session and all data.
	 *
	 * @access public
	 * @return void
	 */
	public function initForum() {
		$user_id = $this->Controller->Auth->user('id');

		if (!$this->Session->check('Forum.isBrowsing')) {
			$isSuper = false;
			$isAdmin = false;
			$highestAccess = 0;
			$accessLevels = array();
			$profile = array();
			$moderates = array();
			$lastVisit = date('Y-m-d H:i:s');

			if ($user_id && $this->Controller->Auth->user($this->config['userMap']['status']) != $this->config['statusMap']['banned']) {
				$access = ClassRegistry::init('Forum.Access')->getListByUser($user_id);
				$highestAccess = 1;

				if ($access) {
					foreach ($access as $level) {
						$accessLevels[$level['AccessLevel']['id']] = $level['AccessLevel']['level'];

						if ($level['AccessLevel']['level'] > $highestAccess) {
							$highestAccess = $level['AccessLevel']['level'];
						}

						if ($level['AccessLevel']['isSuper'] && !$isSuper) {
							$isSuper = true;
						}

						if ($level['AccessLevel']['isAdmin'] && !$isAdmin) {
							$isAdmin = true;
						}
					}
				}

				$moderates = ClassRegistry::init('Forum.Moderator')->getModerations($user_id);
				$profile = ClassRegistry::init('Forum.Profile')->getUserProfile($user_id);
				$profile = $profile['Profile'];
				$lastVisit = $profile['lastLogin'];
			}

			$this->Session->write('Forum.profile', $profile);
			$this->Session->write('Forum.access', $highestAccess);
			$this->Session->write('Forum.accessLevels', $accessLevels);
			$this->Session->write('Forum.isSuper', $isSuper);
			$this->Session->write('Forum.isAdmin', $isAdmin);
			$this->Session->write('Forum.moderates', $moderates);
			$this->Session->write('Forum.lastVisit', $lastVisit);
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
	public function goToPage($topic_id = null, $post_id = null, $return = false) {
		$topic = ClassRegistry::init('Forum.Topic')->getById($topic_id);
		$slug = !empty($topic['Topic']['slug']) ? $topic['Topic']['slug'] : null;

		// Certain page
		if ($topic_id && $post_id) {
			$posts = ClassRegistry::init('Forum.Post')->getIdsForTopic($topic_id);
			$totalPosts = count($posts);
			$perPage = $this->settings['posts_per_page'];

			if ($totalPosts > $perPage) {
				$totalPages = ceil($totalPosts / $perPage);
			} else {
				$totalPages = 1;
			}

			if ($totalPages <= 1) {
				$url = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug, '#' => 'post-' . $post_id);
			} else {
				$posts = array_values($posts);
				$flips = array_flip($posts);
				$position = $flips[$post_id] + 1;
				$goTo = ceil($position / $perPage);
				$url = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug, 'page' => $goTo, '#' => 'post-' . $post_id);
			}

		// First post
		} else if ($topic_id && !$post_id) {
			$url = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $slug);

		// None
		} else {
			$url = $this->Controller->referer();

			if (!$url || (strpos($url, 'delete') !== false)) {
				$url = array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index');
			}
		}

		if ($return) {
			return $url;
		} else {
			$this->Controller->redirect($url);
		}
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

		if ($readTopics && is_array($readTopics)) {
			$readTopics[] = $topic_id;
			$readTopics = array_unique($readTopics);
			$this->Session->write('Forum.readTopics', $readTopics);

		} else {
			$this->Session->write('Forum.readTopics', array($topic_id));
		}
	}

	/**
	 * Builds the page title.
	 *
	 * @access public
	 * @return string
	 */
	public function pageTitle() {
		$args = func_get_args();
		array_unshift($args, __d('forum', 'Forum'));
		array_unshift($args, $this->settings['site_name']);

		$this->Controller->set('title_for_layout', implode($this->settings['title_separator'], $args));
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

		if ($topic_id) {
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

		if ($post_id) {
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
	 * @throws NotFoundException
	 * @throws UnauthorizedException
	 * @throws ForbiddenException
	 */
	public function verifyAccess($validators = array()) {

		// Does the data exist?
		if (isset($validators['exists'])) {
			if (empty($validators['exists'])) {
				throw new NotFoundException();
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
			if ($this->Session->read('Forum.access') < $validators['permission']) {
				throw new UnauthorizedException();
			}
		}

		// Is the item locked/unavailable?
		if (isset($validators['status'])) {
			if (!$validators['status']) {
				throw new ForbiddenException();
			}
		}

		// Does the user own this item?
		if (isset($validators['ownership'])) {
			if ($this->Session->read('Forum.isSuper') || $this->Session->read('Forum.isAdmin')) {
				return true;

			} else if ($this->Controller->Auth->user('id') != $validators['ownership']) {
				throw new UnauthorizedException();
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
			if ($this->Session->read('Forum.isAdmin')) {
				return true;
			} else {
				$this->goToPage();
			}
		} else {
			$this->Controller->redirect($this->config['routes']['login']);
		}

		return false;
	}

}
