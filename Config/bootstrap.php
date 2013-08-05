<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Sanitize', 'Utility');

/**
 * Forum critical constants.
 */
define('FORUM_PLUGIN', dirname(__DIR__) . '/');

// Table Prefix
if (!defined('FORUM_PREFIX')) {
	define('FORUM_PREFIX', 'forum_');
}

// Database config
if (!defined('FORUM_DATABASE')) {
	define('FORUM_DATABASE', 'default');
}

/**
 * Current version.
 */
Configure::write('Forum.version', file_get_contents(dirname(__DIR__) . '/version.md'));

/**
 * Customizable layout; defaults to the plugin layout.
 */
Configure::write('Forum.viewLayout', 'forum');

/**
 * List of settings that alter the forum system.
 */
Configure::write('Forum.settings', array(
	'name' => __d('forum', 'Forum'),
	'email' => 'forum@cakephp.org',
	'url' => 'http://milesj.me/code/cakephp/forum',
	'titleSeparator' => ' - ',

	// Topics
	'topicsPerPage' => 20,
	'topicsPerHour' => 3,
	'topicFloodInterval' => 300,
	'topicPagesTillTruncate' => 10,
	'topicDaysTillAutolock' => 21,
	'excerptLength' => 500,

	// Posts
	'postsPerPage' => 15,
	'postsPerHour' => 15,
	'postsTillHotTopic' => 35,
	'postFloodInterval' => 60,

	// Subscriptions
	'enableTopicSubscriptions' => true,
	'enableForumSubscriptions' => true,
	'autoSubscribeSelf' => true,
	'subscriptionTemplate' => '',

	// Ratings
	'enablePostRating' => true,
	'showRatingScore' => true,
	'ratingBuryThreshold' => -25,
	'rateUpPoints' => 1,
	'rateDownPoints' => 1,

	// Misc
	'whosOnlineInterval' => '-15 minutes',
	'enableQuickReply' => true,
	'enableGravatar' => true,
	'censoredWords' => array(),
	'defaultLocale' => 'eng',
	'defaultTimezone' => '-8',
));

/**
 * Add forum specific user field mappings.
 */
Configure::write('User.fieldMap', Configure::read('User.fieldMap') + array(
	'totalTopics'	=> 'topic_count',
	'totalPosts'	=> 'post_count',
	'signature' 	=> 'signature'
));

/**
 * Add model callbacks for admin panel.
 */
Configure::write('Admin.modelCallbacks', Configure::read('Admin.modelCallbacks') + array(
	'Forum.Forum' => array(
		'open' => 'Open %s',
		'close' => 'Close %s'
	),
	'Forum.Topic' => array(
		'open' => 'Open %s',
		'close' => 'Close %s',
		'sticky' => 'Sticky %s',
		'unsticky' => 'Unsticky %s'
	)
));

/**
 * Add overrides for admin CRUD actions.
 */
Configure::write('Admin.actionOverrides', Configure::read('Admin.actionOverrides') + array(
	'Forum.Forum' => array(
		'delete' => array('plugin' => 'forum', 'controller' => 'stations', 'action' => 'admin_delete')
	)
));