# Forum Installation #

There are 2 forms of installation, automated (command line) and manual. I suggest using the automated process. Before you begin, the following requirements are needed: 

* users table - The forum does not provide a users table, you must create that yourself. You can configure the plugin to interact with your users table by editing the values in forum/config/config.php.
* admin routing - Admin prefix routing must be enabled, see below.
* rss parsing - RSS parsing must be enabled to use the RSS feeds, see below.

## Automated ##

For your convenience, there is an automated installation script that you can run through the command line. If you do not have access to a command line, please jump down to the manual installation section.

To begin, upload the forum into your applications plugins folder. Once complete, run the following command in your command line and follow the on screen instructions.

	cake -app /path/to/app install

## Manual ##

The manual process is a bit tedious, so bare with.

### Creating database tables ###

To begin, you need to create all your database tables. You can do this by opening up each SQL file in the plugins config/schema folder. Once opened, you need to find and replace each instance of {prefix} with a prefix for your tables, I highly suggest using a prefix to not conflict with existing tables. I recommend using "forum_" (without the quotes). Once you have done this, simply execute each of these MySQL queries in PhpMyAdmin or your MySQL command line.

### Creating administrators ###

Up next is creating your admin users. All you need to do is get the ID of the user you want to grant admin privileges for (from your users table). If you do not have a user, simply create a new one and use that ID. Once you have an ID, execute the following query.

	INSERT INTO `{prefix}access` (id, user_id, access_level_id, created, modified) VALUES (null, {user_id}, 4, NOW(), NOW());

### Setting up the AppModel ###

After the tables are created, open up the plugins ForumAppModel and change the values for $tablePrefix to the prefix you have chosen, and $useDbConfig to the database configuration you are using (usually default).

## Requirements ##

The final steps are to make sure your routing settings are correct. Append the following code to your app/config/routes.php file.

	Router::parseExtensions('rss');

And that admin prefix routing is enabled in app/config/core.php.

	Configure::write('Routing.prefixes', array('admin'));
