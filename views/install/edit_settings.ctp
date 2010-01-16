
<h2>Step 3: Edit Settings</h2>

<p>Edit the settings to reflect your website.</p>

<?php // Form
echo $form->create(null, array('action' => 'create_tables'));
echo $form->input('site_name');
echo $form->input('site_email');
echo $form->input('site_main_url'); ?>

<div class="submit">
	<?php echo $form->button('Go Back', array('onclick' => 'window.history.go(-1);'));
	echo $form->submit('Create Tables', array('div' => false)); ?>
</div>

<?php echo $form->end(); ?>