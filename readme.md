# Forum v3.2.0 RC3 #

A fully robust and powerful CakePHP forum plugin. Integrates with an external user system, support for topic and forum subscriptions, and many more features listed below.

This version is only compatible with CakePHP 2.0.

## Compatibility ##

* v2.x - CakePHP 1.3
* v3.x - CakePHP 2

## Requirements ##

* PHP 5.2, 5.3
* Utility Plugin (3.2.0 and above) - https://github.com/milesj/cake-utility
* Utils Plugin (3.1.1 and below) - https://github.com/CakeDC/utils

## Contributors ##

* Jose Antonio Sanchez - Spanish Locale
* Kristoffer Darj - Swedish Locale

## Documentation ##

Thorough documentation can be found here: http://milesj.me/code/cakephp/forum

## Features ##

Forums

* Parent and children forums
* Activity and latest post tracking
* Auto-lock old topics
* Enable/disable post count increments
* Individual settings for reading, posting, replying, etc
* Access/private specific forums
* Create topics or polls
* Reply to topics, or quote previous posts
* Topic and forum subscription with email notifications

Moderation

* Moderators, Super Moderators and Admin levels
* Restrict moderators to specific forums
* Inline moderation and management
* Mass processing (Move, Delete, Close, etc)
* Ability to edit and manage users content

Administration

* Full admin panel using CakePHP admin routes
* Requires the administration access level
* Can add, edit, order, delete forums
* Manage all reported content
* Promote, demote, create access levels, staff and moderators
* Edit forum settings through the panel
* Manage all aspects and content

Security

* Utilizes CakePHPs Auth and Security Components
* Advanced flood protection (Posts and Topics)
* Hourly post limitations
* Registration security wall
* Form protection against bots and hackers

Miscellaneous

* BBCode support using Decoda
* Utilizes remember me features using AutoLogin
* Mark topics as read (Session)
* Log created topics and posts (Session)
* Access level verification
* Report system for topics, posts and users
* Search through topics (Title and Post)
* RSS feeds for each forum
