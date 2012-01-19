
DROP TABLE IF EXISTS `{prefix}reported`;

CREATE TABLE `{prefix}reported` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`item_id` INT(11) DEFAULT NULL,
	`itemType` SMALLINT(6) NOT NULL,
	`user_id` INT(11) DEFAULT NULL,
	`comment` VARCHAR(255) NOT NULL,
	`created` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `item_id` (`item_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reported topics, posts, users, etc' AUTO_INCREMENT=1;
