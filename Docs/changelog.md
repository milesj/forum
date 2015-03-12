# Changelog #

*These logs may be outdated or incomplete.*

## 5.0.5 ##

* Includes changes from previous versions
* Updated to latest FontAwesome
* Updated to jQuery instead of MooTools
* Added missing `parent::beforeRender();` to controllers
* Fixed many bugs regarding entity existence
* Fixed a bug where `Validateable` was not set on `ForumAppModel`

## 5.0.0 ##

* New design and layout using Titon Toolkit
* Integrated more seamlessly with Admin v1.1.0
* Includes the new post rating and access system from v4.1.0

## 4.1.0 ##

* Added new `access` setting to `ForumToolbar.verifyAccess()`
* Added a new post rating feature which allows for up down scoring of posts
* Added email template support to subscriptions through `Forum.settings.subscriptionTemplate`
* Added custom image icon support to forums
* Refactored `Forum` table so that `accessRead`, `accessPost`, `accessReply`, and `accessPoll` all point to ARO records
* Removed `aro_id` column from `Forum` (use `accessRead` instead)
* Renamed certain session variables to be prefixed with Acl instead of Forum
* Replaced `ForumHelper.gravatar()` with `UtilityHelper.gravatar()`

## 4.0.2 ##

* Includes changes from previous minor versions
* Updated Utility to v1.5.0
* Updated shell colors to be easier to read

## 4.0.0 ##

* Replaced the custom ACL system with CakePHP's ACL
* Replaced the admin system with the [Admin plugin](http://milesj.me/code/cakephp/admin)
* Replaced the reporting system with the Admin plugin reporting system
* Replaced the logging system with the Admin plugin logging system
* Integrated the new [Utility 1.4](https://github.com/milesj/Utility) features
* Integrated the `TreeBehavior` where applicable
* Rewrote the `InstallShell` and `UpgradeShell`
* Removed the `Profile` model (the old fields should now be part of the users table)
* Removed Spanish and Swedish locales (too many strings changed)
* Removed user profiles (all user related code should be handled outside of the forum)
* Added a `ForumUser` model that extends from the base `User` model
* Added counter caching for topics and posts for the `User` model
* Added counter caching for poll votes and options

## 3.3.1 ##

* Fixed an issue with moderator and topic delete actions being accessible

## 3.3.0 ##

* Requires PHP 5.3 and Composer
* Upgraded Utility plugin to 1.3.x
* Upgraded Decoda to 5.x
* Added FORUM_DATABASE and FORUM_PREFIX constants
* Added avatar mapping support in Forum.userMap.avatar
* Added support for layout overrides through Forum.viewLayout
* Updated shells, models and controllers to use the new FORUM_* constants
* Updated to HTML5 doctype
* Fixed localization not changing
* Fixed search not working for guests
* Replaced jQuery with Mootools
* Replaced Markitup with Decoda
* Replaced database settings with Forum.settings and Configure
* Renamed CommonHelper to ForumHelper
* Renamed settings to be camelCase
* Moved around search and login forms
* Lots of polish and fixes

## 3.2.0 ##

* Added Composer support
* Added the ability to name the users table during installation
* Added a FORUM_USER constant to define the name of the User model
* Updated to use the Utility plugin
* Updated jQuery, Markitup and Decoda
* Updated to use exceptions
* Implemented Cacheable, Filterable and Enumerable behaviors
* Fixed broken RSS feeds
* Fixed error handling pages
* Removed Utils plugin dependency

## 3.1.1 ##

* Added Swedish translation support (thanks to kristofferdarj)
* Added App::uses() to controllers and models to fix include errors
* Updated Decoda to 3.3.3

## 3.1 ##

* Added Spanish locale (courtesy of jasanchez)
* Updated Decoda to 3.3.1
* Updated TypeConverter to 1.3
* Updated AutoLoginComponent to 3.5.1

## 3.0 ##

* Updated to CakePHP 2.0 (not backwards compatible with 1.3)
* Updated Decoda to 3.3
* Updated AjaxHandlerComponent to 2.0.1
* Added a way to load custom configuration outside of config.php by creating a custom.php file
* Changed ToolbarComponent to ForumToolbarComponent to not conflict with DebugKit
* Removed cakeError() usages and used the standard HTTP exceptions

## 2.3 ##

* Added status to the admin users list
* Added statusMap as a type for CommonHelper::options()
* Added a way to quickly activate/ban a user from the admin panel
* Changed it so topic count is always increased regardless of settingPostCount
* Fixed a bug where topic creation won't auto subscribe
* Fixed instances where Forum.userMap wasn't being used
* Fixed user list to only display active users
* Removed join date from topic viewing
* Removed the login() and logout() methods from UsersController
* Updated the Forum.routes config
* Updated ToolbarComponent to use the Forum.routes.login route

## 2.2 ##

* Added a subscriptions system for topics and forums ([Issue #10](https://github.com/milesj/cake-forum/issues/10))
* Added a UpgradeShell which can be executed via console to handle complex version upgrades
* Added a SubscriptionShell that can be setup via cron jobs to send user subscriptions at specific intervals
* Added the AjaxHandlerComponent and TypeConverter to manage AJAX requests
* Added topic subscriptions to the users dashboard
* Refactored the InstallShell to use ConnectionManager::getDataSource() for all database queries instead of a Model, which fixes association errors ([Issue #11](https://github.com/milesj/cake-forum/issues/11))
* Fixed any minor issues

## 2.1 ##

* Added a users dashboard
* Added Decoda validation during topic and post creation
* Fixed the problem with using $this->here for form action URLs ([Issue #9](https://github.com/milesj/cake-forum/issues/9))
* Updated Decoda to v3.1

## 2.0 ##

* Rewrote the whole plugin from the ground up (Not backwards compatible)
* Rewrote all controllers, models, views, helpers and components
* Rewrote the admin panel
* Rewrote the install system as a Shell instead of a Controller
* Rewrote the user system to use an external users table
* Rewrote the settings to use a table instead of flatfile
* Rewrote the search system
* Renaming forum categories to stations
* Removed the GeSHi code coloring
* Removed all forum translations as they are outdated
* Added a report user system
* Added a new theme and stylesheet
* Added a new Decoda system
* Added new translation files

## 1.9 ##

* Converted to Cake 1.3
* Converted all topics and forums to slug based URLs
* Added CakeSession to all models
* Added a getTotal() method to all models
* Added caching support to Model::find()
* Added a profiles table to store user information separate from the users table
* Refactored HomeController to ForumController (default plugin routing)
* Refactored all classes and views
* Merging the forum and forum_categories tables
* Fixed a bug in the Sluggable
* Fixed a bug where private forums were showing up for public users
* Fixed wrong slugs being used in some URLs
* New users will inherit the forums locale
* Settings are now stored in the database instead of Configure
* Redirect will not redirect back to delete methods
* Removed the hashing method to rely on the Apps setting
* Updating to Cake 1.3 standards
* Updating AutoLogin to 2.0
* Updating jQuery to 1.5

## 1.8 ##

* Rewrote the installation process from the ground up
* Added tons more support for using an external users table
* Added a system to patch an old installation, create admin users and upgrade to newer versions
* Added slugs to topics, forums, forum categories to allow for pretty URLs
* Added the sluggable behavior to support slugs
* Added a shell called slugify to process data with empty slugs
* Added new HTML to certain pages for better CSS styling
* Fixed a bug in post deletion
* Fixed the topic review to sort in DESC
* Fixed a problem where it was using PHP 5.3 constants, now supports 5.2
* BBCode quote tags now support a date attribute

## 1.7 ##

* Built an installer script which can be used at (if not installed yet): /forum/install
* Do NOT upgrade the AppModel or UserModel without a fresh install, or manually remove the {:prefix} from your models

## 1.6 ##

* Added the jQuery MarkItUp plugin for inline textarea BBCode support
* Updated all the views to support the new formatting

## 1.5 ##

* Added the following locales: Bulgarian, Indonesian, Russian
* You can now set a default language within the admin settings
* 

## 1.4 ##

* Turned the config class into a singleton
* Fixed a problem where _autoLogin() was failing so the persistent login was not working
* Added a getLocales() and gravatar() method to the Cupcake Helper
* Gravatar can now be used for avatars and can be toggled within settings
* Integration of i18n/l10n support throughout the whole application
* - German language supported (Translated by Andrew Mortensen)
* - Spanish language supported (Translated by Walter Benavides)

## 1.3 ##

* Code has been upgraded to PHP5 only (Sorry PHP4 users, will need to use 1.1)
* Fixed a bug where regular members could not create topics and could view moderator capabilities
* Fixed a problem where the upgrade SQL was incomplete, now full SQL exists
* Added more info to a users profile page: access levels and what forums they moderate
* Added a censored words feature by utilizing the new Decoda 2.6 (can edit in settings)

## 1.2 ##

* Changed the levels of the default levels (Admin is now 10) to allow more customization of in between access levels
* Added access levels to forums and forum categories to restrict access to certain access leveled users
* Added an _initSession() method to AppController to better handle session data when using the AutoLogin
* Added the ability to report users (Report button within their profile)
* Added new isAdmin, isSuperMod and isBrowsing variables to the session
* Added the goTo() function to the Javascript
* Fixed a warning error on the reported users in admin
* Fixed many database table problems
* Rewrote the access_levels table to better identify admins and super mods
* Rewrote how access is given when using hasAccess() in the helper and component
* Rewrote all the controllers to handle the new access system improvements
* Removed the $accessMap variable from ForumConfig (Shouldn't have been there anyways)
* Updated the admin section to reflect the new access system
* Updated the SQL schemas with the new changes / added a version upgrade SQL

## 1.1 ##

* Added the AutoLogin component to implement a "Remember Me" feature
* Added the _autoLogin() method to support the new AutoLogin
* Added the $html->docType() to all the layouts
* Added inline login forms to certain pages (instant login and redirect)
* Added support for multi-byte and UTF-8 characters
* Added the geshi folders to vendors
* Upgraded to Decoda 2.5 to implement GeSHi syntax highlighting
* Renamed initForum() to _initForum() so that it could not be called within the address bar

## 1.0.85 Beta ##

* Fixed a problem where the App would error if you didn't have the Javascript Helper initially
* Fixed a bug when an infinite loop would occur when deleting posts, topics, etc
* Fixed a bug with read topics throwing an in_array() error
* Added support for multi-byte and UTF-8 characters
* Added a quick-reply feature that can be enabled/disabled with a setting
* Added a new feature where new posts/topics updated the main forum and its parents activity

## 1.0.75 Beta ##

* First initial release of Cupcake
