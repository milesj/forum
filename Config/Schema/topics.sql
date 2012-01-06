
DROP TABLE IF EXISTS `{prefix}topics`;

CREATE TABLE `{prefix}topics` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`forum_id` INT(11) DEFAULT NULL,
	`user_id` INT(11) DEFAULT NULL,
	`title` VARCHAR(100) NOT NULL,
	`slug` VARCHAR(110) NOT NULL,
	`status` SMALLINT(6) NOT NULL DEFAULT '0',
	`type` SMALLINT(6) NOT NULL DEFAULT '0',
	`post_count` INT(11) NOT NULL DEFAULT '0',
	`view_count` INT(11) NOT NULL DEFAULT '0',
	`firstPost_id` INT(11) DEFAULT NULL,
	`lastPost_id` INT(11) DEFAULT NULL,
	`lastUser_id` INT(11) DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `firstPost_id` (`firstPost_id`),
	KEY `lastPost_id` (`lastPost_id`),
	KEY `lastUser_id` (`lastUser_id`),
	KEY `forum_id` (`forum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Discussion topics' AUTO_INCREMENT=1;
