/* Add the slug feature in 1.8 */

ALTER TABLE `{:prefix}forums`
  	ADD `slug` VARCHAR(60) NOT NULL AFTER `title`;

ALTER TABLE `{:prefix}forum_categories`
  	ADD `slug` VARCHAR(60) NOT NULL AFTER `title`;

ALTER TABLE `{:prefix}topics`
  	ADD `slug` VARCHAR(110) NOT NULL AFTER `title`;