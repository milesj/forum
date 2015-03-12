# Forum #

*Documentation may be outdated or incomplete as some URLs may no longer exist.*

*Warning! This codebase is deprecated and will no longer receive support; excluding critical issues.*

The Forum is a slimmed down version of the popular bulletin board system, packaged into a plugin. The plugin can be dropped into any CakePHP application and will run smoothly with a bit of configuration. The plugin comes bundled with all the basic features of a stand-alone system, some of which include topics, posts, users, polls, moderators, staff and more. For a full list, view the chapter regarding Features.

### Requirements ###

* [Utility Plugin](https://github.com/milesj/Utility)
* [Admin Plugin](https://github.com/milesj/Admin)

## Features ##

Below you can find a detailed list containing most of the features within the plugin. To better understand each feature, you should download and install the plugin!

**Forums**
* Parent and children forums
* Activity and latest post tracking
* Auto-Lock old topics
* Enable/Disable post count increments
* Individual settings for reading, posting, replying, etc
* Access/Private specific forums
* Create topics or polls
* Reply to topics, or quote previous posts
* Topic and forum subscription with email notifications
* Post up and down ratings

**Moderation**
* Moderators, Super Moderators and Admin levels
* Restrict moderators to specific forums
* Inline moderation and management
* Mass processing (Move, Delete, Close, etc)
* Ability to edit and manage users content

**Administration**
* Full admin panel using CakePHP Admin plugin
* Utilizes ACL for permissions
* Can add, edit, update, and delete all data
* Manage reported content
* View admin logs and activity

**Security**
* Utilizes CakePHPs Auth and Security Components
* Advanced flood protection (Posts and Topics)
* Hourly post limitations
* Registration security wall
* Form protection against bots and hackers

**Miscellaneous**
* BBCode support using Decoda
* Utilizes remember me features using AutoLogin
* Mark topics as read (Session)
* Log created topics and posts (Session)
* Access level verification
* Report system for topics, posts and users
* Search through topics (Title and Post)
* RSS feeds for each forum

## Installation ##

The plugin must use [Composer](http://getcomposer.org/) for installation so that all dependencies are also installed. [Learn more about using Composer in CakePHP](http://milesj.me/blog/read/using-composer-in-cakephp). Composer will automatically install the forum into the `Plugin` folder.

```javascript
{
    "config": {
        "vendor-dir": "Vendor"
    },
    "require": {
        "mjohnson/forum": "4.*"
    }
}
```

Be sure to enable Composer at the top of `Config/core.php`.

```php
require_once dirname(__DIR__) . '/Vendor/autoload.php';
```

And to load the plugins (bootstrap and routes required).

```php
// Utility should be loaded first
CakePlugin::load('Utility', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Admin', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Forum', array('bootstrap' => true, 'routes' => true));
```

 [Learn more about loading plugins correctly!](http://milesj.me/blog/read/plugin-loading-quirks)

Please review the User Setup and Configuration steps before installing the database tables.

## User Setup ##

The forum plugin was designed to interact with an external users system and does not provide one. This allows for instant modularization and the ability to drop the forum into an already existent application with ease. However, there are a few things that will need to be done. If a users table does not exist, create one with these minimum fields: id, username, email, password and status (active, pending, banned, etc). 

### Model Setup ###

If the application allows the deletion of users, the model associations for the forum will need to be defined so that cascading deletion can be used. These associations are optional.

```php
class User extends AppModel {
    public $hasMany = array(
        'ForumModerator' => array('className' => 'Forum.Moderator'),
        'ForumPollVote' => array('className' => 'Forum.PollVote'),
        'ForumPost' => array('className' => 'Forum.Post'),
        'ForumPostRating' => array('className' => 'Forum.PostRating'),
        'ForumSubscription' => array('className' => 'Forum.Subscription'),
        'ForumTopic' => array('className' => 'Forum.Topic'),
    );
}
```

### Controller Setup ###

The forum **does not** come packaged with a login, logout, signup or user management system. This should be handled within the application outside of the forum. 

Furthermore, the login and logout actions should reset the session, as to properly assign the correct values when the user visits the forum.

```php
$this->Session->destroy();
```

## Configuration ##

Like any system, the forum comes with a wide array of configuration settings. Update the configuration by calling `Configure::write()` *after* the plugin has been loaded within `Config/bootstrap.php`. There are 3 *optional* constants that must be defined *before* the plugin is loaded.

```php
// Optional constants before plugin loading
define('USER_MODEL', 'User'); // Name of the user model (supports plugin syntax)
define('FORUM_PREFIX', 'forum_'); // Table prefix, must not be empty
define('FORUM_DATABASE', 'default'); // Database config to create tables in

// Load the plugins after loadAll()
CakePlugin::loadAll();

// The Utility and Admin plugin must be loaded before the Forum
CakePlugin::load('Utility', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Admin', array('bootstrap' => true, 'routes' => true));
CakePlugin::load('Forum', array('bootstrap' => true, 'routes' => true));

// Configure the plugin after it has been loaded
Configure::write('Forum.settings', array(
    'name' => 'Website Name',
    'email' => 'email@website.com',
    'url' => 'http://website.com'
) + Configure::read('Forum.settings'));
```

### Integrating with a users table ###

If the users table has different column names than the ones defined in the plugin, one can override the settings in the bootstrap. The `User.fieldMap` (Forum.userMap in v3.x) contains a mapping of specific fields that the plugin uses, while the `User.statusMap` (Forum.statusMap in v3.x) are mappings of a users state. Below is an example of some custom mappings.

```php
Configure::write('User.fieldMap', array(
    'username' => 'user',
    'password' => 'pass',
    'email'    => 'mail',
    'status'   => 'active',
    'avatar'   => 'picture'
) + Configure::read('User.fieldMap'));

Configure::write('User.statusMap', array(
    'pending' => 0,
    'active'  => 1,
    'banned'  => 2
) + Configure::read('User.statusMap'));
```

### Overriding the views and layout ###

To change the layout, override the `Forum.viewLayout` option. The default layout is `forum`.

```php
Configure::write('Forum.viewLayout', 'default');
```

To use custom views, [create new template files in the designated plugin view structure](http://book.cakephp.org/2.0/en/plugins.html#overriding-plugin-views-from-inside-your-application). In regards to the forum, the view path would be: `app/View/Plugin/Forum/Controller/action.ctp`.

### Modifying the settings ###

The forum comes pre-bundled with a list of settings that include flood intervals, page limits, site title, site email, security questions and many more. One can modify these settings by overriding `Forum.settings` with Configure. [The full list of settings can be found within the plugins bootstrap file](https://github.com/milesj/Forum/blob/master/Config/bootstrap.php).

When editing multiple settings with an array, be sure to merge in the current settings array.

```php
Configure::write('Forum.settings.whosOnlineInterval', '-5 minutes');

Configure::write('Forum.settings', array(
    'topicsPerPage' => 25,
    'postsPerPage' => 20
) + Configure::read('Forum.settings'));
```

## Database Setup ##

Before creating the database tables, be sure the previous steps have been completed!

For convenience, there is an automated installation script that can be run through the command line that will create all tables and records. Simply run the following command in your command line and follow the on screen instructions.

The [Admin plugin](http://milesj.me/code/cakephp/admin) must be installed before the Forum.

```bash
// Install the Admin first
app/Console/cake Admin.install

// Install the Forum second
app/Console/cake Forum.install
```

## Translation ##

To translate the forum into a new language, or fix a previous language, fork the repository and follow the instructions below. This requires PoEdit: [http://poedit.net/](http://poedit.net/)

Open the program and navigate to "File > New catalog from POT file" and load up the `Forum/Locale/forum.pot` file. Fill in the settings box with the following information:

```
Project: Forum Plugin
Team: <Your Name>
Team Email: <Your Email>
Language: <Pick the language>
Country: <Pick the country>
Charset: UTF-8
Source code: UTF-8
Plural: <Leave blank>
```

Create a folder for your language within the Locale folder, [based on the 3 character locale](http://www.loc.gov/standards/iso639-2/php/code_list.php). Within the language folder, create the LC_MESSAGES folder and save the file as `Forum/Locale/{locale}/LC_MESSAGES/forum.po`. 

Once saved, the program should populate with the text on the left. Translate the sentences on the left by typing in the translation box at the bottom. Messages contain tokens like %s, %d, %count% that will be replaced with dynamic data; they are **required**.

```
%d = Integer/Number
%s = Text/String

"The forum %s has been deleted, and all its forums have been moved!"
"The forum General Discussion has been deleted, and all its forums have been moved!"
```

Save again. Two files should now exist after saving, a po (text) and mo (binary) file. Verify the contents of the translations before pushing up to the repository. Once pushed, open up a Github pull request.
