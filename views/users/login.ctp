
<div class="forumHeader">
	<h2><?php __d('forum', 'Login'); ?></h2>
</div>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login'))); ?>
<?php echo $this->Form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $this->Form->input('password', array('label' => __d('forum', 'Password', true), 'type' => 'password')); ?>
<?php echo $this->Form->input('auto_login', array('type' => 'checkbox', 'label' => __d('forum', 'Remember Me?', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Login', true)); ?>