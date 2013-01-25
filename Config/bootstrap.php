<?php
/**
 * Forum - Configuration
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Sanitize', 'Utility');

/**
 * Name of the User model.
 */
if (!defined('FORUM_USER')) {
	define('FORUM_USER', 'User');
}

/**
 * Current version.
 */
Configure::write('Forum.version', file_get_contents(dirname(__DIR__) . '/version.md'));

/**
 * A map of user fields that are used within this plugin. If your users table has a different naming scheme
 * for the username, email, status, etc fields, you can define their replacement here.
 */
Configure::write('Forum.userMap', array(
	'username'	=> 'username',
	'password'	=> 'password',
	'email'		=> 'email',
	'status'	=> 'status',
	'avatar'	=> 'avatar'
));

/**
 * A map of status values for the users "status" column.
 * This column determines if the user is pending, currently active, or banned.
 */
Configure::write('Forum.statusMap', array(
	'pending'	=> 0,
	'active'	=> 1,
	'banned'	=> 2
));

/**
 * A map of external user management URLs.
 */
Configure::write('Forum.routes', array(
	'login' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'login'),
	'logout' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'logout'),
	'signup' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'signup'),
	'forgotPass' => array('plugin' => false, 'admin' => false, 'controller' => 'users', 'action' => 'forgot_password')
));

/**
 * Customizable view settings. This allows for layout and template overrides.
 */
Configure::write('Forum.view', array(
	'layout' => 'forum',
	'path' => null
));

/**
 * List of settings that alter the forum systems.
 */
Configure::write('Forum.settings', array(
	'name' => __d('forum', 'Forum Plugin'),
	'email' => '',
	'url' => '',
	'securityQuestion' => __d('forum', 'What framework does this plugin run on?'),
	'securityAnswer' => 'cakephp',
	'titleSeparator' => ' - ',

	// Topics
	'topicsPerPage' => 20,
	'topicsPerHour' => 3,
	'topicFloodInterval' => 300,
	'topicPagesTillTruncate' => 10,
	'topicDaysTillAutolock' => 21,

	// Posts
	'postsPerPage' => 15,
	'postsPerHour' => 15,
	'postsTillHotTopic' => 35,
	'postFloodInterval' => 60,

	// Subscriptions
	'enableTopicSubscriptions' => true,
	'enableForumSubscriptions' => true,
	'autoSubscribeSelf' => true,

	// Misc
	'whosOnlineInterval' => 15,
	'enableQuickReply' => true,
	'enableGravatar' => true,
	'censoredWords' => array(),
	'defaultLocale' => 'eng',
	'defaultTimezone' => '-8',
));

/**
 * List of all timezones.
 */
Configure::write('Forum.timezones', array(
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
));

/**
 * List of translated locales.
 */
Configure::write('Forum.locales', array(
	'eng' => 'English',
	'spa' => 'Spanish',
	'swe' => 'Swedish',
	/*'deu' => 'German',
	'fre' => 'French',
	'rus' => 'Russian',
	'ind' => 'Indonesian',
	'bul' => 'Bulgarian'*/
));

/**
 * List of Cake locales to Decoda locales.
 */
Configure::write('Forum.decodaLocales', array(
	'eng' => 'en-us',
	'spa' => 'es-mx',
	'swe' => 'sv-se',
	/*'deu' => 'de-de',
	'fre' => 'fr-fr',
	'rus' => 'ru-ru',
	'ind' => 'id-id',
	'bul' => 'bg-bg'*/
));

/**
 * Handle exceptions and errors.
 */
Configure::write('Exception.renderer', 'Forum.ForumExceptionRenderer');