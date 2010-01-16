
<h2>Step 2: Database Table Check</h2>

<?php // Database connected
if ($isConnected) { ?>

	<p>Check to see if there are any conflicts between tables in your current database.</p>

	<p>Tables to be created: <i><?php echo implode(', ', $tables); ?></i></p>

	<?php // No conflicts!
	if (empty($taken)) { ?>
		<div class="successBox">
			<b>Success!</b> There are no table conflicts when using the prefix: <?php echo $this->data['prefix']; ?>.
		</div>

		<br />
		<p>Do you have an already existent user table? Check the following field to <b>not</b> prefix the user table.</p>

		<?php // Form
		echo $form->create(null, array('action' => 'edit_settings'));
		echo $form->input('user_table', array('type' => 'checkbox', 'label' => 'User Table Exists', 'after' => ' Yes')); ?>

		<div class="submit">
			<?php echo $form->button('Go Back', array('onclick' => 'window.history.go(-1);'));
			echo $form->submit('Edit Settings', array('div' => false)); ?>
		</div>

		<?php echo $form->end();
	} else { ?>
		
		<div class="errorBox">
			<b>Error:</b> The following tables are in conflict: <?php echo implode(', ', $taken); ?>.
		</div>

		<div class="submit">
			<?php echo $form->button('Go Back', array('onclick' => 'window.history.go(-1);')); ?>
		</div>

	<?php }
} else { ?>

	<div class="errorBox">
		<b>Error:</b> There is no connection to the database you selected!
	</div>

	<div class="submit">
		<?php echo $form->button('Go Back', array('onclick' => 'window.history.go(-1);')); ?>
	</div>

<?php } ?>