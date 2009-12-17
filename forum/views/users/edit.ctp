
<h2><?php __d('forum', 'Edit Profile'); ?></h2>

<?php $session->flash(); ?>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit'))); ?>
<?php echo $form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $form->input('locale', array('options' => $cupcake->getLocales(), 'label' => __d('forum', 'Language', true))); ?>
<?php echo $form->input('timezone', array('options' => $cupcake->getTimezones(), 'label' => __d('forum', 'Timezone', true))); ?>
<?php echo $form->input('signature', array('label' => __d('forum', 'Signature', true), 'type' => 'textarea', 'rows' => 5)); ?>
<?php echo $form->end(__d('forum', 'Update Account', true)); ?>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit'))); ?>
<?php echo $form->input('oldPassword', array('type' => 'password', 'label' => __d('forum', 'Old Password', true))); ?>
<?php echo $form->input('newPassword', array('type' => 'password', 'label' => __d('forum', 'New Password', true))); ?>
<?php echo $form->input('confirmPassword', array('type' => 'password', 'label' => __d('forum', 'Confirm Password', true))); ?>
<?php echo $form->end(__d('forum', 'Update Password', true)); ?>