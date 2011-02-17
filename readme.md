# Forum v1.9.1 #

A fully robust and powerful CakePHP forum plugin.

## Requirements ##

* CakePHP 1.3.x
* PHP 5.2.x

## Documentation ##

Thorough documentation can be found here: http://milesj.me/resources/script/forum-plugin

## Installation ##

* Clone the repo into a folder called "forum" within your CakePHP's plugins directory.
* Hit the installer script at: www.yourdomain.com/forum/install/

## Contributors ##

* Mihail Ilinov - Bulgarian
* Andrew Mortensen - German
* Emmanuel Henke - French
* Rachman Chavik - Indonesian
* Alexey Kondratyev - Russian
* Walter Mairena - Spanish
* Mariano Iglesias - Sluggable Behavior

## Features ##

### Forums ###
* Forum wrappers and forum categories
* First tier sub-forums
* Activity and latest post tracking
* Auto-Lock old topics
* Enable/Disable post count increments
* Individual settings for reading, posting, replying, etc
* Access/Private specific forums
* Create topics or polls
* Reply to topics, or quote previous posts

### Moderation ###
* Moderators, Super Moderators and Admin levels
* Restrict moderators to specific forums
* Inline moderation and management
* Mass processing (Move, Delete, Close, etc)
* Ability to edit and manage users content

### Administration ###
* Full admin panel using CakePHP admin routes
* Requires the administration access level
* Can add, edit, order, delete forums and categories
* Manage all reported content
* Promote, demote, create access levels, staff and moderators
* Edit forum settings through the panel
* Manage all aspects and content

### Security ###
* Utilizes CakePHPs Auth and Security Components
* Advanced flood protection (Posts and Topics)
* Hourly post limitations
* Registration security wall
* Form protection against bots and hackers

### Miscellaneous ###
* BBCode support using the Decoda Helper
* Uses the GeSHi class for code syntax highlighting
* Utilizes remember me features
* Mark topics as read (Session)
* Log created topics and posts (Session)
* Access level verification
* Report system for topics, posts and users
* Search through topics (Title and Post)
* RSS feeds for each forum

## Translating ##

If you would like to translate the forum into a new language, or fix a previous language.
Simply fork the repo and follow the instructions below, you will need to download PoEdit: http://www.poedit.net/

Once you have done that, just follow these steps.

1 - Open the program and go to File > New catalog from POT file
2 - Fill in the settings box with the following information

	Project: Cupcake Forum Plugin
	Team: Miles Johnson
	Team Email: contact@milesj.me
	Language: <Pick the language>
	Country: <Pick the country>
	Charset: UTF-8
	Source code: UTF-8
	Plural: <Leave blank>

3 - Hit ok
4 - Save the file as forum.po and place it anywhere
5 - Once saved, the program should populate with the text on the left
6 - Translate the sentences on the left by typing in the translation box in the bottom
7 - When you come across text with the %s and %d symbols, that means those symbols will be replaced with dynamic data

	Example:
	%d = Integer/Number
	%s = Text/String

	"The forum %s has been deleted, and all its forum categories have been moved!"
	"The forum General Discussion has been deleted, and all its forum categories have been moved!"

So those ARE REQUIRED to work correctly, just place those symbols where they would be within the translated string.
You may find some others like %count% and %total%; leave those as they are as well.

8 - When you are done, simply save the file
9 - You should now have 2 files, a .po and a .mo
10 - Open up the .po with Notepad and you should see the following

	#: /controllers/categories_controller.php:101
	msgid "A total of %d topic(s) have been permanently deleted"
	msgstr "<YOUR TRANSLATED VERSION HERE>"

11 - Create a folder for your language within the locale folder, based on the 3 character locale: http://www.loc.gov/standards/iso639-2/php/code_list.php
12 - Within the language folder, create the LC_MESSAGES folder and place the .po file within.
13 - Commit and push! Once done, I will review and merge with your changes.
