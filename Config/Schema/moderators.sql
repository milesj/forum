
DROP TABLE IF EXISTS `{prefix}moderators`;

CREATE TABLE `{prefix}moderators` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`forum_id` INT(11) DEFAULT NULL,
	`user_id` INT(11) DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Moderators to forums' AUTO_INCREMENT=1;
