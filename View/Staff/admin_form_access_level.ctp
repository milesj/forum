<?php

if ($method === 'add') {
	$action = 'add_access_level';
	$button = __d('forum', 'Save');
	$title = __d('forum', 'Add Access Level');
} else {
	$action = 'edit_access_level';
	$button = __d('forum', 'Update');
	$title = __d('forum', 'Edit Access Level');
}

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<?php if ($method === 'edit' && $this->data['AccessLevel']['id'] <= 4) { ?>

	<p><?php echo __d('forum', 'You are unable to edit core levels, they are restricted.'); ?></p>

<?php } else {
	echo $this->Form->create('AccessLevel'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('title', array('label' => __d('forum', 'Title')));
		echo $this->Form->input('level', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Access Level'), 'empty' => false));
		echo $this->Form->input('isSuper', array('options' => $this->Common->options('status'), 'label' => __d('forum', 'Is Super Moderator?'), 'empty' => false));
		echo $this->Form->input('isAdmin', array('options' => $this->Common->options('status'), 'label' => __d('forum', 'Is Administrator?'), 'empty' => false)); ?>
	</div>
</div>

<?php
	echo $this->Form->submit($button, array('class' => 'button'));
	echo $this->Form->end();
} ?>