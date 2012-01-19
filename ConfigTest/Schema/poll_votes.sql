
DROP TABLE IF EXISTS `{prefix}poll_votes`;

CREATE TABLE `{prefix}poll_votes` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`poll_id` INT(11) DEFAULT NULL,
	`poll_option_id` INT(11) DEFAULT NULL,
	`user_id` INT(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `poll_id` (`poll_id`),
	KEY `poll_option_id` (`poll_option_id`),
	KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Votes for polls' AUTO_INCREMENT=1;
