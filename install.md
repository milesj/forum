# Forum Installation #

To begin, upload the forum into your applications plugins folder.

## 1. Creating your users table ##

If you already have a users table, please verify the configuration for $userMap and $statusMap in forum/config/config.php.

If you do not have a users table, please create one with these minimum fields: id, username, email, password and status (active, pending, banned, etc). You may name the columns as you please as long as you configure it correctly in the $userMap and $statusMap in forum/config/config.php.

## 2. Modifying your user model ##

Once your users table and user model is created, add the following to the user model:

	public $hasOne = array('Forum.Profile');

	public $hasMany = array('Forum.Access', 'Forum.Moderator');

## 3. Preparing your app ##

Admin routing must be enabled in app/config/core.php.

	Configure::write('Routing.prefixes', array('admin'));

As well as RSS parsing in app/config/routes.php.

	Router::parseExtensions('rss');

And finally applying these routes.

	Router::connect('/forum/help/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'help'));
	Router::connect('/forum/rules/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'rules'));
	Router::connect('/admin/forum/settings/*', array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'settings', 'admin' => true));

It is also a good idea to create a "forum" folder within your app/tmp/cache folder, and chmod it to 777.

## 4. Run the installer ##

For your convenience, there is an automated installation script that you can run through the command line. If you do not have access to a command line, please jump down to the manual installation section. Now run the following command in your command line and follow the on screen instructions.

	cake -app /path/to/app install

# Manual Installation #

The manual process is a bit tedious, so bare with.

### Creating database tables ###

To begin, you need to create all your database tables. You can do this by opening up each SQL file in the forum/config/schema folder. Once opened, you need to find and replace each instance of {prefix} with a prefix for your tables, I highly suggest using a prefix to not conflict with existing tables. I recommend using "forum_" (without the quotes). Once you have done this, simply execute each of these MySQL queries in PhpMyAdmin or your MySQL command line.

### Creating administrators ###

Up next is creating your admin users. All you need to do is get the ID of the user you want to grant admin privileges for (from your users table). If you do not have a user, simply create a new one and use that ID. Once you have an ID, execute the following query. Be sure to replace the {prefix} and {user_id} values.

	INSERT INTO `{prefix}access` (id, user_id, access_level_id, created, modified) VALUES (null, {user_id}, 4, NOW(), NOW());

### Setting up the AppModel ###

After the tables are created, open up the plugins ForumAppModel and change the values for $tablePrefix to the prefix you have chosen, and $useDbConfig to the database configuration you created the tables in (usually default).
