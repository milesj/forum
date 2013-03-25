
DROP TABLE IF EXISTS `{prefix}forums`;

CREATE TABLE `{prefix}forums` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`parent_id` INT(11) DEFAULT NULL,
	`aro_id` INT(11) DEFAULT NULL,
	`title` VARCHAR(100) NOT NULL,
	`slug` VARCHAR(115) NOT NULL,
	`description` VARCHAR(255) NOT NULL,
	`status` SMALLINT(6) NOT NULL DEFAULT '1',
	`orderNo` SMALLINT(6) NOT NULL DEFAULT '0',
	`topic_count` INT(11) NOT NULL DEFAULT '0',
	`post_count` INT(11) NOT NULL DEFAULT '0',
	`accessRead` TINYINT(1) NOT NULL DEFAULT '1',
	`accessPost` TINYINT(1) NOT NULL DEFAULT '1',
	`accessPoll` TINYINT(1) NOT NULL DEFAULT '1',
	`accessReply` TINYINT(1) NOT NULL DEFAULT '1',
	`autoLock` TINYINT(1) NOT NULL DEFAULT '1',
	`lastTopic_id` INT(11) DEFAULT NULL,
	`lastPost_id` INT(11) DEFAULT NULL,
	`lastUser_id` INT(11) DEFAULT NULL,
	`lft` INT(11) DEFAULT NULL,
	`rght` INT(11) DEFAULT NULL,
	`created` DATETIME DEFAULT NULL,
	`modified` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `lastTopic_id` (`lastTopic_id`),
	KEY `lastPost_id` (`lastPost_id`),
	KEY `lastUser_id` (`lastUser_id`),
	KEY `parent_id` (`parent_id`),
	KEY `aro_id` (`aro_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Forum categories to post topics to' AUTO_INCREMENT=1;

INSERT INTO `{prefix}forums` (`id`, `parent_id`, `aro_id`, `title`, `slug`, `description`, `status`, `orderNo`, `topic_count`, `post_count`, `accessRead`, `accessPost`, `accessReply`, `accessPoll`, `autoLock`, `lastTopic_id`, `lastPost_id`, `lastUser_id`, `lft`, `rght`, `created`, `modified`) VALUES
	(1, null, null, 'Forums', 'forums', 'This is a primary forum and it contains child forums. Primary forums (no parents) can not be posted in.', 1, 1, 0, 0, 1, 1, 1, 1, 0, null, null, null, 1, 4, NOW(), NOW()),
	(2, 1, null, 'General Discussion', 'general-discussion', 'This is a child forum. You can add, edit or delete these forums by visiting the administration panel, but first you would need to give a user admin rights.', 1, 1, 0, 0, 1, 1, 1, 1, 1, null, null, null, 2, 3, NOW(), NOW());
