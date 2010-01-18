
<h2>Step 3: Create Tables</h2>

<p>Your tables are now being created in the database: <b><?php echo $database; ?></b></p>

<p>Tables created (or altered): <b><?php echo $executed; ?></b> of <b><?php echo $total; ?></b></p>

<?php if ($executed == $total) {
	echo $form->create(null, array('action' => 'finished')); ?>

	<div class="submit">
		<?php echo $form->button('Go Back', array('onclick' => 'window.history.go(-1);'));
		echo $form->submit('Finish Installation', array('div' => false)); ?>
	</div>

<?php echo $form->end();
} else { ?>

<div class="errorBox">
	<b>Error:</b> There was an error creating the database tables. You will need to completely remove the tables manually before trying the installation process again.
</div>

<?php } ?>