<?php

$this->Breadcrumb->add(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Breadcrumb->add(__d('forum', 'Login'), array('controller' => 'users', 'action' => 'login')); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Login'); ?></h2>
</div>

<?php echo $this->Form->create('User'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input($config['userMap']['username'], array('label' => __d('forum', 'Username')));
		echo $this->Form->input($config['userMap']['password'], array('label' => __d('forum', 'Password'), 'type' => 'password'));
		echo $this->Form->input('auto_login', array('type' => 'checkbox', 'format' => array('label', 'input'), 'label' => __d('forum', 'Remember Me?'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Login'), array('class' => 'button'));
echo $this->Form->end(); ?>