
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

# Add post ratings
ALTER TABLE `{prefix}posts`
	ADD `up` INT(11) NOT NULL DEFAULT '0' AFTER `content`,
	ADD `down` INT(11) NOT NULL DEFAULT '0' AFTER `up`,
	ADD `score` INT(11) NOT NULL DEFAULT '0' AFTER `down`;

CREATE TABLE `{prefix}post_ratings` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`post_id` INT(11) NOT NULL,
	`topic_id` INT(11) NOT NULL,
	`type` SMALLINT(6) NOT NULL,
	`created` DATETIME DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `user_id` (`user_id`),
	KEY `post_id` (`post_id`),
	KEY `topic_id` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Up down ratings for posts' AUTO_INCREMENT=1 ;