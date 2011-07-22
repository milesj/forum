<?php

if ($method == 'add') {
	$action = 'add_moderator';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Moderator', true);
} else {
	$action = 'edit_moderator';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Moderator', true);
}

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff', true), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php printf(__d('forum', 'To find the users ID, you can search for them on the %s.', true), $this->Html->link(__d('forum', 'Users listing', true), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>

<?php echo $this->Form->create('Moderator', array('url' => $this->here)); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('user_id', array('type' => 'text', 'class' => 'numeric', 'label' => __d('forum', 'User ID', true)));
		echo $this->Form->input('forum_id', array('label' => __d('forum', 'Forum', true), 'empty' => true, 'escape' => false)); ?>
	</div>
</div>

<?php 
echo $this->Form->submit($button, array('class' => 'button'));
echo $this->Form->end(); ?>
