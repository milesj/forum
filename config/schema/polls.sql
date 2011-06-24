
DROP TABLE IF EXISTS `{prefix}polls`;

CREATE TABLE `{prefix}polls` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`topic_id` INT(11) DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	`expires` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Polls attached to topics' AUTO_INCREMENT=1;
