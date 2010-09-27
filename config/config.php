<?php
/**
 * Forum - Core Configuration
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */

/**
 * Current version: http://milesj.me/resources/logs/forum-plugin
 */
$config['Forum']['version'] = '2.0';

/**
 * A map of user fields that are used within this plugin. If your users table has a different naming scheme
 * for the username, email, status, etc fields, you can define their replacement here.
 */
$config['Forum']['userMap'] = array(
	'username'	=> 'username',
	'email'		=> 'email',
	'status'	=> 'status'
);

/**
 * A map of status values for the users "status" column. This column determines if the user is pending,
 * currently active, or banned.
 */
$config['Forum']['statusMap'] = array(
	'pending'	=> 0,
	'active'	=> 1,
	'banned'	=> 2
);