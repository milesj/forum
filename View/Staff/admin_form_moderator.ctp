<?php

if ($method === 'add') {
	$action = 'add_moderator';
	$button = __d('forum', 'Save');
	$title = __d('forum', 'Add Moderator');
} else {
	$action = 'edit_moderator';
	$button = __d('forum', 'Update');
	$title = __d('forum', 'Edit Moderator');
}

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php printf(__d('forum', 'To find the users ID, you can search for them on the %s.'), $this->Html->link(__d('forum', 'Users listing'), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>

<?php echo $this->Form->create('Moderator'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		if ($method === 'add') {
			echo $this->Form->input('user_id', array('type' => 'text', 'class' => 'numeric', 'label' => __d('forum', 'User ID')));
		} else {
			echo $this->Form->input('User.' . $config['userMap']['username'], array('type' => 'text',  'label' => __d('forum', 'User'), 'readonly' => true));
		}
		echo $this->Form->input('forum_id', array('label' => __d('forum', 'Forum'), 'empty' => true)); ?>
	</div>
</div>

<?php
echo $this->Form->submit($button, array('class' => 'button'));
echo $this->Form->end(); ?>
