
UPDATE `{prefix}forums` SET `accessRead` = 1 WHERE `accessRead` = 0;
UPDATE `{prefix}forums` SET `accessPost` = 1 WHERE `accessPost` != 0;
UPDATE `{prefix}forums` SET `accessPoll` = 1 WHERE `accessPoll` != 0;
UPDATE `{prefix}forums` SET `accessReply` = 1 WHERE `accessReply` != 0;

DROP TABLE `{prefix}access`, `{prefix}access_levels`, `{prefix}settings`;

ALTER TABLE `{prefix}forums` CHANGE `access_level_id` `aro_id` INT(11) NULL DEFAULT NULL;