
DROP TABLE IF EXISTS `{prefix}subscriptions`;

CREATE TABLE `{prefix}subscriptions` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`topic_id` INT(11) DEFAULT NULL,
	`forum_id` INT(11) DEFAULT NULL,
	`user_id` INT(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `forum_id` (`forum_id`),
	KEY `topic_id` (`topic_id`)	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Subscription Data' AUTO_INCREMENT=1;
