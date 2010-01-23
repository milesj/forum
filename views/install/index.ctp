
<div class="forumHeader">
	<h2>Step 1: Database Configuration</h2>
</div>

<p>Select the database you want to use, and the prefix to append to your table names.</p>

<p>Furthermore, do you have an already existent user table? Check the following field to <b>not</b> prefix the user table.</p>

<?php // Form
echo $form->create(null, array('action' => 'check_database'));
echo $form->input('database', array('options' => $databases));
echo $form->input('prefix');
echo $form->input('user_table', array('type' => 'checkbox', 'label' => 'Use Existent User Table', 'after' => ' Yes'));
echo $form->end('Check Database'); ?>