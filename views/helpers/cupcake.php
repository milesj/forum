<?php
/** 
 * cupcake.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Cupcake Helper
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */

App::import('Core', 'HttpSocket');
 
class CupcakeHelper extends AppHelper {

	/**
	 * Helpers
	 * @access public
	 * @var array
	 */
	public $helpers = array('Html', 'Session');

	/**
	 * Array of current gravatars to try and limit HTTP requests
	 * @access private
	 * @var array
	 */
	private $__gravatars = array();
	
	/**
	 * Load forum settings
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$Config = ForumConfig::getInstance();
		$this->version = $Config->version;
		$this->settings = $Config->settings;
	}

	/**
	 * Determine the forum icon state
	 * @access public
	 * @param array $category
	 * @return string
	 */
	public function forumIcon($category) { 
		$icon = 'open';
		
		if (isset($category['LastPost']['created'])) {
			$lastPost = $category['LastPost']['created'];
		} else if (isset($category['LastTopic']['created'])) {
			$lastPost = $category['LastTopic']['created'];
		}
		
		if ($category['status'] == 1) {
			$icon = 'closed';
		} else if (isset($lastPost) && $lastPost > $this->Session->read('Forum.lastVisit')) {
			$icon = 'new';
		}
		
		return $this->Html->image('/forum/img/forum_'. $icon .'.png', array('alt' => ucfirst($icon)));
	}
	
	/**
	 * Gets the highest access level
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
	 * Get an array of the supported locales.
	 * @access public
	 * @return array
	 */
	public function getLocales() {
		$locales = array();
		/*$data = explode("\n", $this->settings['supported_locales']);

		foreach ($data as $key) {
			if (strpos($key, '=') !== false) {
				list($locale, $title) = explode('=', $key);
				$locales[$locale] = $title;
			}
		}*/

		// http://www.loc.gov/standards/iso639-2/php/code_list.php
		if (empty($locales)) {
			$locales = array(
				'eng' => 'English',
				'deu' => 'German',
				'fre' => 'French',
				'spa' => 'Spanish',
				//'dut' => 'Dutch',
				'rus' => 'Russian',
				'ind' => 'Indonesian'
			);
		}

		return $locales;
	}

	/**
	 * List of timezones
	 * @access public
	 * @return array
	 */
	public function getTimezones() {
		return array(
			'-12'	=> '(GMT -12:00) International Date Line West',
			'-11'	=> '(GMT -11:00) Midway Island',
			'-10'	=> '(GMT -10:00) Hawaii',
			'-9'	=> '(GMT -9:00) Alaska',
			'-8'	=> '(GMT -8:00) Pacific Time',
			'-7'	=> '(GMT -7:00) Mountain Time',
			'-6'	=> '(GMT -6:00) Central Time',
			'-5'	=> '(GMT -5:00) Eastern Time',
			'-4'	=> '(GMT -4:00) Atlantic Time',
			'-3'	=> '(GMT -3:00) Greenland',
			'-2'	=> '(GMT -2:00) Brazil, Mid-Atlantic',
			'-1'	=> '(GMT -1:00) Portugal',
			'0'		=> '(GMT +0:00) Greenwich Mean Time',
			'+1'	=> '(GMT +1:00) Germany, Italy, Spain',
			'+2'	=> '(GMT +2:00) Greece, Israel, Turkey, Zambia',
			'+3'	=> '(GMT +3:00) Iraq, Kenya, Russia (Moscow)',
			'+4'	=> '(GMT +4:00) Azerbaijan, Afghanistan, Russia (Izhevsk)',
			'+5'	=> '(GMT +5:00) Pakistan, Uzbekistan',
			'+5.5'	=> '(GMT +5:30) India, Sri Lanka',
			'+6'	=> '(GMT +6:00) Bangladesh, Bhutan',
			'+6.5'	=> '(GMT +6:30) Burma, Cocos',
			'+7'	=> '(GMT +7:00) Thailand, Vietnam',
			'+8'	=> '(GMT +8:00) China, Malaysia, Taiwan, Australia',
			'+9'	=> '(GMT +9:00) Japan, Korea, Indonesia',
			'+9.5'	=> '(GMT +9:30) Australia',
			'+10'	=> '(GMT +10:00) Australia, Guam, Micronesia',
			'+11'	=> '(GMT +11:00) Solomon Islands, Vanuatu',
			'+12'	=> '(GMT +12:00) New Zealand, Fiji, Nauru',
			'+13'	=> '(GMT +13:00) Tonga'
		);
	}
	
	/**
	 * Get topics made in the past hour
	 * @access public
	 * @return int
	 */
	public function getTopicsMade() {
		$topics = $this->Session->read('Forum.topics');
		$pastHour = strtotime('-1 hour');
			
		$count = 0;
		if (!empty($topics)) {
			foreach ($topics as $id => $time) {
				if ($time >= $pastHour) {
					++$count;
				}
			}
		}
		
		return $count;
	}
	
	/**
	 * Get posts made in the past hour
	 * @access public
	 * @return int
	 */
	public function getPostsMade() {
		$posts = $this->Session->read('Forum.posts');
		$pastHour = strtotime('-1 hour');
			
		$count = 0;
		if (!empty($posts)) {
			foreach ($posts as $id => $time) {
				if ($time >= $pastHour) {
					++$count;
				}
			}
		}
		
		return $count;
	}

	/**
	 * Generates a gravatar image
	 * @param string $email
	 * @param int $size
	 * @param string $rating
	 * @return string
	 */
	public function gravatar($email, $size = 100, $rating = 'g') {
		$email = md5(strtolower($email));
		
		if (isset($this->__gravatars[$email])) {
			if ($this->__gravatars[$email] != null) {
				return $this->Html->image($this->__gravatars[$email]);
			}
		} else {
			$properties = array('default' => 404);

			if (!empty($size)) {
				$properties['size'] = $size;
			}

			if (!empty($rating)) {
				$properties['rating'] = strtolower($rating);
			}

			$url = 'http://www.gravatar.com/avatar/'. $email;
			$query = http_build_query($properties);

			$HttpSocket = new HttpSocket();
			$response = $HttpSocket->get($url, $query);
			$gravatar = $url .'?'. $query;

			if ($response != '404 Not Found') {
				$this->__gravatars[$email] = $gravatar;
				return $this->Html->image($gravatar);
			} else {
				$this->__gravatars[$email] = null;
			}
		}

		return;
	}
	
	/**
	 * Checks to see if the user has mod status
	 * @access public
	 * @param string $level
	 * @param int $forum_id
	 * @return boolean 
	 */
	public function hasAccess($level = 1, $forum_id = NULL) { 
		if (($this->Session->read('Forum.isSuperMod') >= 1) || ($this->Session->read('Forum.isAdmin') >= 1)) {
			return true;
		} else if ($level == 'super' || $level == 'admin') {
			return false;
		}
		
		if (!empty($forum_id) && $level == 'mod') {
			if (in_array($forum_id, $this->Session->read('Forum.moderates'))) {
				return true;
			} else {
				return false;
			}
		}
		
		return ($this->getAccess() >= $level) ? true : false;
	}
	
	/**
	 * Prebuilt option lists for form selects
	 * @access public
	 * @param int $type
	 * @param string $value
	 * @param boolean $guest
	 * @return array|string
	 */
	public function options($type = 1, $value = '', $guest = false) {
		if ($type == 1) {
			$options = array(0 => __d('forum', 'No', true), 1 => __d('forum', 'Yes', true));
		} else if ($type == 2) {
			$options = array(0 => __d('forum', 'Open', true), 1 => __d('forum', 'Closed', true));
		} else if ($type == 3) {
			$options = array(0 => __d('forum', 'Visible', true), 1 => __d('forum', 'Hidden', true));
		} else if ($type == 4) {
			$options = array(
				1 => '1 ('. __d('forum', 'Member', true) .')',
				2 => '2',
				3 => '3',
				4 => '4 ('. __d('forum', 'Moderator', true) .')',
				5 => '5',
				6 => '6',
				7 => '7 ('. __d('forum', 'Super Moderator', true) .')',
				8 => '8',
				9 => '9',
				10 => '10 ('. __d('forum', 'Administrator', true) .')'
			);
			
			if ($guest) {
				array_unshift($options, '0 ('. __d('forum', 'Guest', true) .')');
			}
		} else if ($type == 5) {
			$options = array(0 => __d('forum', 'Active', true), 1 => __d('forum', 'Banned', true));
		}
		
		if (isset($options[$value])) {
			return $options[$value];
		} else {
			return $options;
		}
	}

	/**
	 * Get the users timezone
	 * @return string
	 */
	public function timezone() {
		if ($this->Session->check('Auth.User.timezone')) {
			return $this->Session->read('Auth.User.timezone');
		}
	}

	/**
	 * Determine the topic icon state
	 * @access public
	 * @param array $topic
	 * @return string
	 */
	public function topicIcon($topic) {
		$lastVisit = $this->Session->read('Forum.lastVisit');
		$readTopics = $this->Session->read('Forum.readTopics');
		
		if (!is_array($readTopics)) {
			$readTopics = array();
		}
		
		$icon = 'open';
		
		if (isset($topic['LastPost']['created'])) {
			$lastPost = $topic['LastPost']['created'];
		} else if (isset($topic['Topic']['created'])) {
			$lastPost = $topic['Topic']['created'];
		}
		
		if ($topic['Topic']['status'] == 1) {
			$icon = 'closed';
		} else {
			if (isset($lastPost) && $lastPost > $lastVisit &&  !in_array($topic['Topic']['id'], $readTopics)) {
				$icon = 'new';
			} else if ($topic['Topic']['type'] == 1) {
				$icon = 'sticky';
			} else if ($topic['Topic']['type'] == 2) {
				$icon = 'important';
			} else if ($topic['Topic']['type'] == 3) {
				$icon = 'announcement';
			}
		}
		
		if ($icon == 'open' || $icon == 'new') {
			if ($topic['Topic']['post_count'] >= $this->settings['posts_till_hot_topic']) {
				$icon .= '_hot';
			}
		}
		
		return $this->Html->image('/forum/img/topic_'. $icon .'.png', array('alt' => ucfirst($icon)));
	}
		
	/**
	 * Get the amount of pages for a topic
	 * @access public
	 * @param array $topic
	 * @return array
	 */
	public function topicPages($topic) {
		if (empty($topic['page_count'])) {
			$postsPerPage = $this->settings['posts_per_page'];
			$topic['page_count'] = ($topic['post_count'] > $postsPerPage) ? ceil($topic['post_count'] / $postsPerPage) : 1;
		}
		
		$topicPages = array();
		for ($i = 1; $i <= $topic['page_count']; ++$i) {
			$topicPages[] = $this->Html->link($i, array('controller' => 'topics', 'action' => 'view', $topic['id'], 'page' => $i));
		}
		
		if ($topic['page_count'] > $this->settings['topic_pages_till_truncate']) {
			array_splice($topicPages, 2, $topic['page_count'] - 4, '...');
		}
		
		return $topicPages;
	}
	
	/**
	 * Get the type of topic
	 * @access public
	 * @param int $type
	 * @return string
	 */
	public function topicType($type) {
		switch ($type) {
			case 0:	$t = ''; break;
			case 1: $t = __d('forum', 'Sticky', true) .':'; break;
			case 2: $t = __d('forum', 'Important', true) .':'; break;
			case 3: $t = __d('forum', 'Announcement', true) .':'; break;
		}
		
		return $this->output('<strong>'. $t .'</strong>');
	}
	
	/**
	 * Checks to see if a user is logged in
	 * @access public
	 * @param string $key
	 * @return boolean|string
	 */
	public function user($key = '') {
		if (empty($key)) {
			return $this->Session->check('Auth.User');
		}
		
		if ($key == 'id') {
			$user_id = $this->Session->read('Auth.User.id');
			
			if (is_numeric($user_id) && $user_id > 0) {
				return $user_id;
			}
		} else {
			if ($this->Session->check('Auth.User.'. $key)) {
				return $this->Session->read('Auth.User.'. $key);
			}
		}
		
		return false;
	}

}
