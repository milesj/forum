
<h2><?php __d('forum', 'Forgot Password'); ?></h2>

<p><?php __d('forum', 'Please enter either your username or email to retrieve your information. Once you retrieved, you should receive an email with your login credentials.'); ?></p>

<?php $session->flash(); ?>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgot'))); ?>
<?php echo $form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $form->end(__d('forum', 'Retrieve', true)); ?>