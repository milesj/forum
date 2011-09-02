
DROP TABLE IF EXISTS `{prefix}poll_options`;

CREATE TABLE `{prefix}poll_options` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`poll_id` INT(11) DEFAULT NULL,
	`option` VARCHAR(100) NOT NULL,
	`vote_count` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Options/Questions for a poll' AUTO_INCREMENT=1;
