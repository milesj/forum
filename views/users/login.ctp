
<div class="forumHeader">
	<h2><?php __d('forum', 'Login'); ?></h2>
</div>

<?php 
echo $this->Form->create('User', array('url' => $this->here));
echo $this->Form->input($config['userMap']['username'], array('label' => __d('forum', 'Username', true)));
echo $this->Form->input($config['userMap']['password'], array('label' => __d('forum', 'Password', true), 'type' => 'password'));
echo $this->Form->input('auto_login', array('type' => 'checkbox', 'label' => __d('forum', 'Remember Me?', true)));
echo $this->Form->end(__d('forum', 'Login', true)); ?>