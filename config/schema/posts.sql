
DROP TABLE IF EXISTS `{prefix}posts`;

CREATE TABLE `{prefix}posts` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`forum_id` INT(11) DEFAULT NULL,
	`topic_id` INT(11) DEFAULT NULL,
	`user_id` INT(11) DEFAULT NULL,
	`userIP` VARCHAR(100) NOT NULL,
	`content` text NOT NULL,
	`contentHtml` text NOT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `forum_id` (`forum_id`),
	KEY `topic_id` (`topic_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Posts to topics' AUTO_INCREMENT=1;
