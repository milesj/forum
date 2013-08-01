
DROP TABLE IF EXISTS `{prefix}post_ratings`;

CREATE TABLE `{prefix}post_ratings` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`post_id` INT(11) NOT NULL,
	`topic_id` INT(11) NOT NULL,
	`type` SMALLINT(6) NOT NULL,
	`created` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `post_id` (`post_id`),
	KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Up down ratings for posts' AUTO_INCREMENT=1 ;