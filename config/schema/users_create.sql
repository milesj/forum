--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(30) NOT NULL,
	`password` VARCHAR(50) NOT NULL,
	`email` VARCHAR(30) NOT NULL,
	`status` SMALLINT(6) NOT NULL DEFAULT '0',
	`signature` VARCHAR(255) NOT NULL,
	`locale` VARCHAR(3) NOT NULL DEFAULT 'eng',
	`timezone` VARCHAR(4) NOT NULL DEFAULT '-8',
	`totalPosts` INT(10) NOT NULL,
	`totalTopics` INT(10) NOT NULL,
	`currentLogin` DATETIME DEFAULT NULL,
	`lastLogin` DATETIME DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users' AUTO_INCREMENT=1 ;