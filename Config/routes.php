<?php
/**
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

/**
 * Custom Forum routes.
 */
Router::connect('/forum.rss', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index', 'ext' => 'rss'));
Router::connect('/forum/help/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'help'));
Router::connect('/forum/rules/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'rules'));
