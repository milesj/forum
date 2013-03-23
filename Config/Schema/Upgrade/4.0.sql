
UPDATE `{prefix}forums` SET `accessRead` = 1 WHERE `accessRead` = 0;
UPDATE `{prefix}forums` SET `accessPost` = 1 WHERE `accessPost` != 0;
UPDATE `{prefix}forums` SET `accessPoll` = 1 WHERE `accessPoll` != 0;
UPDATE `{prefix}forums` SET `accessReply` = 1 WHERE `accessReply` != 0;

DROP TABLE `{prefix}access`, `{prefix}access_levels`, `{prefix}settings`, `{prefix}reported`;

ALTER TABLE `{prefix}forums`
	CHANGE `forum_id` `parent_id` INT( 11 ) NULL DEFAULT NULL,
	CHANGE `access_level_id` `aro_id` INT(11) NOT NULL DEFAULT '0',
	ADD `lft` INT NULL DEFAULT NULL AFTER `lastUser_id`,
	ADD `rght` INT NULL DEFAULT NULL AFTER `lft`;

ALTER TABLE `{prefix}poll_options`
	CHANGE `vote_count` `poll_vote_count` INT( 11 ) NOT NULL DEFAULT '0',
	ADD `created` DATETIME NULL DEFAULT NULL ,
	ADD `modified` DATETIME NULL DEFAULT NULL;

ALTER TABLE `{prefix}poll_votes` ADD `created` DATETIME NULL DEFAULT NULL ;