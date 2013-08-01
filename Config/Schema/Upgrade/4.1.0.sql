
# Update forum access to use AROs
UPDATE `{prefix}forums` SET `accessRead` = `aro_id`;

ALTER TABLE `{prefix}forums`
	CHANGE `accessRead` `accessRead` INT NULL DEFAULT NULL,
	CHANGE `accessPost` `accessPost` INT NULL DEFAULT NULL,
	CHANGE `accessPoll` `accessPoll` INT NULL DEFAULT NULL,
	CHANGE `accessReply` `accessReply` INT NULL DEFAULT NULL,
	ADD INDEX ( `accessRead` ),
	ADD INDEX ( `accessPost` ),
	ADD INDEX ( `accessPoll` ),
	ADD INDEX ( `accessReply` ),
	DROP `aro_id`;