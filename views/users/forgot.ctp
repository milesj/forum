
<div class="forumHeader">
	<h2><?php __d('forum', 'Forgot Password'); ?></h2>
</div>

<p><?php __d('forum', 'Please enter either your username or email to retrieve your information. Once you retrieved, you should receive an email with your login credentials.'); ?></p>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'forgot'))); ?>
<?php echo $this->Form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $this->Form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Retrieve', true)); ?>