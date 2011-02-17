
<div class="forumHeader">
	<h2>Step 1: Database Configuration</h2>
</div>

<?php // Form posted
if ($processed) {

	// Database connected
	if ($isConnected) {
		if (empty($conflicts)) { ?>
			<div class="successBox">
				<b>Success!</b> There are no table conflicts when using the prefix: <?php echo $this->data['prefix']; ?>
			</div>
		<?php } else { ?>
			<div class="errorBox">
				<b>Error:</b> The following tables are in conflict: <?php echo implode(', ', $conflicts); ?>
			</div>
		<?php }
		
	} else { ?>
		<div class="errorBox">
			<b>Error:</b> There is no connection to the database you selected!
		</div>
	<?php }
} ?>

<p>Select the database you want to use, and the prefix to append to your table names (recommended).</p>

<?php // Form
echo $this->Form->create(null, array('action' => 'check_database'));
echo $this->Form->input('database', array('options' => $databases));
echo $this->Form->input('prefix'); ?>

<div class="submit">
	<?php echo $this->Form->submit('Check Database', array('div' => false)); ?>

	<?php if ($processed && empty($conflicts)) {
		echo $this->Form->button('Create Tables', array('onclick' => "goTo('". Router::url(array('action' => 'create_tables')) ."');", 'type' => 'button', 'class' => 'button'));
	} ?>
</div>

<?php echo $this->Form->end(); ?>
