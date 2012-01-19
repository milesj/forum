
DROP TABLE IF EXISTS `{prefix}subscriptions`;

CREATE TABLE `{prefix}subscriptions` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) DEFAULT NULL,
	`forum_id` INT(11) DEFAULT NULL,
	`topic_id` INT(11) DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `topic_id` (`topic_id`),
	KEY `forum_id` (`forum_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User topic and forum subscriptions.' AUTO_INCREMENT=1;