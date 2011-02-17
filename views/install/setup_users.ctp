
<div class="forumHeader">
	<h2>Step 3: Setup Users Table</h2>
</div>

<?php // Form posted
if ($processed && $executed) { ?>
	<div class="successBox">
		<b>Success!</b>

		<?php if ($this->data['action'] == 'sqlCreate') { ?>
		Your users table has successfully been created.
		<?php } else { ?>
		Your users table has successfully been altered.
		<?php } ?>
	</div>

	<p>Your table is now being processed into the database: <b><?php echo $database; ?></b></p>

	<?php // Altered
	if ($this->data['action'] == 'sqlAlter') { ?>
		<p>Since you altered your users table, its highly required that you alter the $columnMap property in your UserModel. You should change the values to match the columns in your database, <b>only if you made manual changes to the schema</b>.</p>

		<pre class="php decoda_code">/**
 * A column map allowing you to define the name of certain user columns.
 *
 * @access public
 * @var array
 */
public $columnMap = array(
	'status'		=> 'status',
	'signature'		=> 'signature',
	'locale'		=> 'locale', // Must allow 3 characters: eng
	'timezone'		=> 'timezone', // Must allow 5 digits: -10.5
	'totalPosts'	=> 'totalPosts',
	'totalTopics'	=> 'totalTopics',
	'currentLogin'	=> 'currentLogin',
	'lastLogin'		=> 'lastLogin'
);</pre>
		
		<br /><br />
	<?php } ?>

	<div class="submit">
		<?php echo $this->Form->button('Finish Installation', array('onclick' => "goTo('". Router::url(array('action' => 'finished')) ."');", 'type' => 'button', 'class' => 'button')); ?>
	</div>

<?php // Didn't execute
} else {
	if ($processed && !$executed) { ?>
		<div class="errorBox">
			<b>Error:</b> There was an error executing the users schema.
		</div>
	<?php } ?>

	<p>The forum plugin allows you to use an already existent users table (to integrate into a pre-built app), or create a whole new table specific to the forum.
	You will need to choose whether you want to alter an existing table with the new columns, or create a whole new table.</p>

	<p><b>If you are creating a new table...</b><br />
	The schema has already been prefixed and is ready to be created. Simply choose that option in the dropdown.</p>

	<p><b>If you are altering an existent table...</b><br />
	You may use the schema below to make changes. For example, if you have a last login timestamp column that is named differently than the one provided, you may remove that command from the schema (changes must be applied in the UserModel manually).
	This applies to all columns in the alter schema, allowing you to fully integrate it into your existent table.</p>

	<?php 
	echo $this->Form->create(null, array('action' => 'setup_users'));
	echo $this->Form->input('sqlCreate', array('type' => 'textarea', 'label' => 'Create Schema'));
	echo $this->Form->input('sqlAlter', array('type' => 'textarea', 'label' => 'Alter Schema'));
	echo $this->Form->input('action', array('type' => 'select', 'options' => array('sqlCreate' => 'Create The Table', 'sqlAlter' => 'Alter The Table')));
	echo $this->Form->end('Process User Table');
} ?>