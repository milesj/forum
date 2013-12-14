
DROP TABLE IF EXISTS `{prefix}profiles`;

CREATE TABLE `{prefix}profiles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `signature` VARCHAR(255) NOT NULL,
    `locale` VARCHAR(3) NOT NULL DEFAULT 'eng',
    `timezone` VARCHAR(4) NOT NULL DEFAULT '-8',
    `totalPosts` INT(10) NOT NULL DEFAULT '0',
    `totalTopics` INT(10) NOT NULL DEFAULT '0',
    `currentLogin` DATETIME DEFAULT NULL,
    `lastLogin` DATETIME DEFAULT NULL,
    `created` DATETIME DEFAULT NULL,
    `modified` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User profiles' AUTO_INCREMENT=1;
