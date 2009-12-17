
<h2><?php __d('forum', 'Login'); ?></h2>

<?php $session->flash(); ?>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'login'))); ?>
<?php echo $form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $form->input('password', array('label' => __d('forum', 'Password', true), 'type' => 'password')); ?>
<?php echo $form->input('auto_login', array('type' => 'checkbox', 'label' => __d('forum', 'Remember Me?', true))); ?>
<?php echo $form->end(__d('forum', 'Login', true)); ?>