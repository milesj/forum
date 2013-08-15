<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('Forum', 'Forum.Model');
App::uses('Topic', 'Forum.Model');
App::uses('Report', 'Forum.Model');

class ForumHelper extends AppHelper {

	/**
	 * Helpers.
	 *
	 * @type array
	 */
	public $helpers = array('Html', 'Session', 'Utility.Decoda', 'Utility.Utility', 'Admin.Admin');

	/**
	 * Output a users avatar.
	 *
	 * @param array $user
	 * @param int $size
	 * @return string
	 */
	public function avatar($user, $size = 100) {
		$userMap = Configure::read('User.fieldMap');
		$avatar = null;

		if (!empty($userMap['avatar']) && !empty($user['User'][$userMap['avatar']])) {
			$avatar = $this->Html->image($user['User'][$userMap['avatar']], array('width' => $size, 'height' => $size));

		} else if (Configure::read('Forum.settings.enableGravatar')) {
			$avatar = $this->Utility->gravatar($user['User'][$userMap['email']], array('size' => $size));
		}

		if ($avatar) {
			return $this->Html->div('avatar', $avatar);
		}

		return $avatar;
	}

	/**
	 * Determine the forum icon state.
	 *
	 * @param array $forum
	 * @param array $options
	 * @return string
	 */
	public function forumIcon($forum, array $options = array()) {
		$options = $options + array(
			'open' => 'icon-envelope-alt',
			'closed' => 'icon-lock',
			'new' => 'icon-envelope'
		);
		$icon = 'open';
		$tooltip = '';

		if (isset($forum['LastPost']['created'])) {
			$lastPost = $forum['LastPost']['created'];

		} else if (isset($forum['LastTopic']['created'])) {
			$lastPost = $forum['LastTopic']['created'];
		}

		if ($forum['status'] == Forum::CLOSED) {
			$icon = 'closed';

		} else if (isset($lastPost) && $lastPost > $this->Session->read('Forum.lastVisit')) {
			$icon = 'new';
		}

		$custom = null;

		if (isset($forum['Forum']['icon'])) {
			$custom = $forum['Forum']['icon'];
		} else if (isset($forum['icon'])) {
			$custom = $forum['icon'];
		}

		if ($custom) {
			return $this->Html->image($custom);
		}

		switch ($icon) {
			case 'open': $tooltip = __d('forum', 'No New Posts'); break;
			case 'closed': $tooltip = __d('forum', 'Closed'); break;
			case 'new': $tooltip = __d('forum', 'New Posts'); break;
		}

		return $this->Html->tag('span', '', array('class' => $options[$icon] . ' js-tooltip', 'data-tooltip' => $tooltip));
	}

	/**
	 * Get topics made in the past hour.
	 *
	 * @return int
	 */
	public function getTopicsMade() {
		$pastHour = strtotime('-1 hour');
		$count = 0;

		if ($topics = $this->Session->read('Forum.topics')) {
			foreach ($topics as $time) {
				if ($time >= $pastHour) {
					++$count;
				}
			}
		}

		return $count;
	}

	/**
	 * Get posts made in the past hour.
	 *
	 * @return int
	 */
	public function getPostsMade() {
		$pastHour = strtotime('-1 hour');
		$count = 0;

		if ($posts = $this->Session->read('Forum.posts')) {
			foreach ($posts as $time) {
				if ($time >= $pastHour) {
					++$count;
				}
			}
		}

		return $count;
	}

	/**
	 * Checks to see if the user has mod status.
	 *
	 * @param string $model
	 * @param string $action
	 * @param int $role
	 * @return bool
	 */
	public function hasAccess($model, $action, $role = null) {
		$user = $this->Session->read('Auth.User');

		if (empty($user)) {
			return false;

		} else if ($this->Admin->isSuper()) {
			return true;

		} else if ($role !== null) {
			if (!$this->Admin->hasRole($role)) {
				return false;
			}
		}

		$has = $this->Admin->hasAccess($model, $action, 'Forum.permissions', true);

		// If permission doesn't exist, they have it by default
		if ($has === null) {
			return true;
		}

		return $has;
	}

	/**
	 * Return true if the user is a forum mod.
	 *
	 * @param int $forum_id
	 * @return bool
	 */
	public function isMod($forum_id) {
		return ($this->Admin->isSuper() || in_array($forum_id, $this->Session->read('Forum.moderates')));
	}

	/**
	 * Return a user profile URL.
	 *
	 * @param array $user
	 * @return string
	 */
	public function profileUrl($user) {
		return $this->Admin->getUserRoute('profile', $user);
	}

	/**
	 * Get the users timezone.
	 *
	 * @return string
	 */
	public function timezone() {
		if ($timezone = $this->Session->read(AuthComponent::$sessionKey . '.' . Configure::read('User.fieldMap.timezone'))) {
			return $timezone;
		}

		return Configure::read('Forum.settings.defaultTimezone');
	}

	/**
	 * Determine the topic icon state.
	 *
	 * @param array $topic
	 * @param array $options
	 * @return string
	 */
	public function topicIcon($topic, array $options = array()) {
		$options = $options + array(
			'open' => 'icon-comment-alt',
			'open-hot' => 'icon-comments-alt',
			'closed' => 'icon-lock',
			'new' => 'icon-comment',
			'new-hot' => 'icon-comments',
			'sticky' => 'icon-question-sign',
			'important' => 'icon-exclamation-sign',
			'announcement' => 'icon-warning-sign'
		);

		$lastVisit = $this->Session->read('Forum.lastVisit');
		$readTopics = $this->Session->read('Forum.readTopics');

		if (!is_array($readTopics)) {
			$readTopics = array();
		}

		$icon = 'open';
		$tooltip = '';

		if (isset($topic['LastPost']['created'])) {
			$lastPost = $topic['LastPost']['created'];
		} else if (isset($topic['Topic']['created'])) {
			$lastPost = $topic['Topic']['created'];
		}

		if (!$topic['Topic']['status'] && $topic['Topic']['type'] != Topic::ANNOUNCEMENT) {
			$icon = 'closed';
		} else {
			if (isset($lastPost) && $lastPost > $lastVisit &&  !in_array($topic['Topic']['id'], $readTopics)) {
				$icon = 'new';
			} else if ($topic['Topic']['type'] == Topic::STICKY) {
				$icon = 'sticky';
			} else if ($topic['Topic']['type'] == Topic::IMPORTANT) {
				$icon = 'important';
			} else if ($topic['Topic']['type'] == Topic::ANNOUNCEMENT) {
				$icon = 'announcement';
			}
		}

		if ($icon === 'open' || $icon === 'new') {
			if ($topic['Topic']['post_count'] >= Configure::read('Forum.settings.postsTillHotTopic')) {
				$icon .= '-hot';
			}
		}

		switch ($icon) {
			case 'open': $tooltip = __d('forum', 'No New Posts'); break;
			case 'open-hot': $tooltip = __d('forum', 'No New Posts'); break;
			case 'closed': $tooltip = __d('forum', 'Closed'); break;
			case 'new': $tooltip = __d('forum', 'New Posts'); break;
			case 'new-hot': $tooltip = __d('forum', 'New Posts'); break;
			case 'sticky': $tooltip = __d('forum', 'Sticky'); break;
			case 'important': $tooltip = __d('forum', 'Important'); break;
			case 'announcement': $tooltip = __d('forum', 'Announcement'); break;
		}

		return $this->Html->tag('span', '', array('class' => $options[$icon] . ' js-tooltip', 'data-tooltip' => $tooltip));
	}

	/**
	 * Get the amount of pages for a topic.
	 *
	 * @param array $topic
	 * @return array
	 */
	public function topicPages($topic) {
		if (empty($topic['page_count'])) {
			$postsPerPage = Configure::read('Forum.settings.postsPerPage');
			$topic['page_count'] = ($topic['post_count'] > $postsPerPage) ? ceil($topic['post_count'] / $postsPerPage) : 1;
		}

		$topicPages = array();

		for ($i = 1; $i <= $topic['page_count']; ++$i) {
			$topicPages[] = $this->Html->link($i, array('controller' => 'topics', 'action' => 'view', $topic['slug'], 'page' => $i));
		}

		if ($topic['page_count'] > Configure::read('Forum.settings.topicPagesTillTruncate')) {
			array_splice($topicPages, 2, $topic['page_count'] - 4, '...');
		}

		return $topicPages;
	}

	/**
	 * Get the type of topic.
	 *
	 * @param int $type
	 * @return string
	 */
	public function topicType($type = null) {
		if (!$type) {
			return null;
		}

		return '<b>' . $this->Utility->enum('Forum.Topic', 'type', $type) . '</b>';
	}

	/**
	 * Modify Decoda before rendering the view.
	 *
	 * @param string $viewFile
	 */
	public function beforeRender($viewFile) {
		$censored = Configure::read('Forum.settings.censoredWords');

		if (is_string($censored)) {
			$censored = array_map('trim', explode(',', $censored));
		}

		$decoda = $this->Decoda->getDecoda();
		$decoda->addFilter(new \Decoda\Filter\BlockFilter(array(
			'spoilerToggle' => "$('spoiler-content-{id}').toggle();"
		)));

		if ($censored) {
			$decoda->getHook('Censor')->blacklist($censored);
		}
	}

}