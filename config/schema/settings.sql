
DROP TABLE IF EXISTS `{prefix}settings`;

CREATE TABLE `{prefix}settings` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`key` VARCHAR(50) NOT NULL,
	`value` VARCHAR(100) NOT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Forum settings' AUTO_INCREMENT=1;

INSERT INTO `{prefix}settings` (`id`, `key`, `value`, `modified`) VALUES
	(NULL, 'site_name', 'CakePHP Forum Plugin', NOW()),
	(NULL, 'site_email', 'noreply@example.com', NOW()),
	(NULL, 'site_main_url', 'http://example.com', NOW()),
	(NULL, 'topics_per_page', 20, NOW()),
	(NULL, 'topics_per_hour', 3, NOW()),
	(NULL, 'topic_flood_interval', 300, NOW()),
	(NULL, 'topic_pages_till_truncate', 10, NOW()),
	(NULL, 'posts_per_page', 15, NOW()),
	(NULL, 'posts_per_hour', 15, NOW()),
	(NULL, 'posts_till_hot_topic', 35, NOW()),
	(NULL, 'post_flood_interval', 60, NOW()),
	(NULL, 'days_till_autolock', 21, NOW()),
	(NULL, 'whos_online_interval', 15, NOW()),
	(NULL, 'security_question', 'What framework does this plugin run on?', NOW()),
	(NULL, 'security_answer', 'cakephp', NOW()),
	(NULL, 'enable_quick_reply', 1, NOW()),
	(NULL, 'enable_gravatar', 1, NOW()),
	(NULL, 'censored_words', '', NOW()),
	(NULL, 'default_locale', 'eng', NOW()),
	(NULL, 'default_timezone', '-8', NOW()),
	(NULL, 'title_separator', ' - ', NOW()),
	(NULL, 'enable_topic_subscriptions', 1, NOW()),
	(NULL, 'enable_forum_subscriptions', 1, NOW()),
	(NULL, 'auto_subscribe_self', 1, NOW());
