<?php
/**
 * Forum - Routes
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

/**
 * Enable admin routes.
 */
Configure::write('Routing.prefixes', array('admin'));

/**
 * Enable RSS feeds.
 */
Router::parseExtensions('rss');

/**
 * Custom Forum routes.
 */
Router::connect('/forum.rss', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index', 'ext' => 'rss'));
Router::connect('/forum/help/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'help'));
Router::connect('/forum/rules/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'rules'));
Router::connect('/forum/user/:id/*', array('plugin' => 'forum', 'controller' => 'users', 'action' => 'profile'), array('pass' => array('id'), 'id' => '[0-9]+'));
Router::connect('/admin/forum/settings/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'settings', 'admin' => true));