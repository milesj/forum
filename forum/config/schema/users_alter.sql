--
-- Table structure for table `users`
--

ALTER TABLE `users` 
  	ADD `status` SMALLINT(6) NOT NULL DEFAULT '0',
	ADD `signature` VARCHAR(255) NOT NULL,
	ADD `locale` VARCHAR(3) NOT NULL DEFAULT 'eng',
	ADD `timezone` VARCHAR(4) NOT NULL DEFAULT '-8',
	ADD `totalPosts` INT(10) NOT NULL,
	ADD `totalTopics` INT(10) NOT NULL,
	ADD `currentLogin` DATETIME DEFAULT NULL,
	ADD `lastLogin` DATETIME DEFAULT NULL;
