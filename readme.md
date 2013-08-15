# Forum v4.1.0 #

A fully robust and powerful CakePHP forum plugin. Integrates with an external user system, support for topic and forum subscriptions, and many more features listed below.

## Requirements ##

* PHP 5.3.0
	* Multibyte
	* Decoda - https://github.com/milesj/Decoda
	* Titon\Utility - https://github.com/titon/Utility
* CakePHP 2
	* Admin Plugin - https://github.com/milesj/Admin
	* Utility Plugin - https://github.com/milesj/Utility
* Composer

## Compatibility ##

* v3.3 - CakePHP 2.x, PHP 5.3, Composer
* v3.x - CakePHP 2.x, PHP 5.2
* v2.x - CakePHP 1.3, PHP 5.2

## Contributors ##

* Jose Antonio Sanchez - Spanish Locale
* Kristoffer Darj - Swedish Locale

## Documentation ##

Thorough documentation can be found here: http://milesj.me/code/cakephp/forum

## Features ##

Forums
* Unlimited Parent and child forums
* Activity and latest post tracking
* Auto-locking of old topics
* Individual settings for reading, posting, replying, etc
* Access/private specific forums through ACL
* Create topics or polls
* Reply to topics, or quote previous posts
* Topic and forum subscription with email notifications
* Post up and down ratings

Moderation
* Moderators, Super Moderators and Admin levels
* Restrict moderators to specific forums
* Inline moderation and management
* Mass processing (Move, Delete, Close, etc)
* Ability to edit and manage users content

Administration
* Full admin panel using CakePHP Admin plugin
* Utilizes ACL for permissions
* Can add, edit, order, delete all data
* Manage all reported content
* Promote, demote, create access levels, staff and moderators
* Manage all aspects and content

Security
* Utilizes CakePHPs Auth and Security Components
* Advanced flood protection (Posts and Topics)
* Hourly post limitations
* Registration security wall
* Form protection against bots and hackers

Miscellaneous
* BBCode support using Decoda
* Mark topics as read (Session)
* Log created topics and posts (Session)
* Report system for topics, posts and users
* Search through topics (Title and Post)
* RSS feeds for each forum
