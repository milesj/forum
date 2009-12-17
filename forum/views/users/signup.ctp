
<h2><?php __d('forum', 'Sign Up'); ?></h2>

<p><?php __d('forum', 'Registration is free. All fields are required.'); ?></p>

<?php $session->flash(); ?>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'signup'))); ?>
<?php echo $form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $form->input('newPassword', array('type' => 'password', 'label' => __d('forum', 'Password', true))); ?>
<?php echo $form->input('confirmPassword', array('type' => 'password', 'label' => __d('forum', 'Confirm Password', true))); ?>
<?php echo $form->input('security', array('after' => ' '. $cupcake->settings['security_question'], 'label' => __d('forum', 'Security Question', true), 'style' => 'width: 10%')); ?>
<?php echo $form->end(__d('forum', 'Sign Up', true)); ?>