
<h2>Step 1: Database Configuration</h2>

<p>Select the database you want to use, and the prefix to append to your table names.</p>

<?php // Form
echo $form->create(null, array('action' => 'check_database'));
echo $form->input('database', array('options' => $databases));
echo $form->input('prefix');
echo $form->end('Check Database'); ?>