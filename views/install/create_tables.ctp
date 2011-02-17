
<div class="forumHeader">
	<h2>Step 2: Create Tables</h2>
</div>

<?php if ($executed == $total) { ?>
<div class="successBox">
	<b>Success!</b> Your tables created succesfully. Continue to setup the users table.
</div>
<?php } else { ?>
<div class="errorBox">
	<b>Error:</b> There was an error creating the database tables. Tables have been dropped.
</div>
<?php } ?>

<p>Your tables are now being created in the database: <b><?php echo $database; ?></b></p>

<p><b>Tables being created:</b> <?php echo implode(', ', $tables); ?></p>

<div class="submit">
	<?php if ($executed == $total) {
		echo $this->Form->button('Setup Users Table', array('onclick' => "goTo('". Router::url(array('action' => 'setup_users')) ."');", 'type' => 'button', 'class' => 'button'));
	} else {
		echo $this->Form->button('Restart', array('onclick' => "goTo('". Router::url(array('action' => 'index')) ."');", 'type' => 'button', 'class' => 'button'));
	} ?>
</div>