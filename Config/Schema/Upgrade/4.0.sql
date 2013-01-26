
UPDATE `{prefix}forums` SET `accessRead` = 1 WHERE `accessRead` != 0;
UPDATE `{prefix}forums` SET `accessPost` = 1 WHERE `accessPost` != 0;
UPDATE `{prefix}forums` SET `accessPoll` = 1 WHERE `accessPoll` != 0;
UPDATE `{prefix}forums` SET `accessReply` = 1 WHERE `accessReply` != 0;