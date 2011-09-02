<?php

if ($method == 'add') {
	$action = 'add_access_level';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Access Level', true);
} else {
	$action = 'edit_access_level';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Access Level', true);
}

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff', true), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<?php if ($method == 'edit' && $this->data['AccessLevel']['id'] <= 4) { ?>

	<p><?php __d('forum', 'You are unable to edit core levels, they are restricted.'); ?></p>

<?php } else {
	echo $this->Form->create('AccessLevel', array('url' => $this->here)); ?>
	
<div class="container">
	<div class="containerContent">
		<?php	
		echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));
		echo $this->Form->input('level', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Access Level', true), 'empty' => false));
		echo $this->Form->input('isSuper', array('options' => $this->Common->options('status'), 'label' => __d('forum', 'Is Super Moderator?', true), 'empty' => false));
		echo $this->Form->input('isAdmin', array('options' => $this->Common->options('status'), 'label' => __d('forum', 'Is Administrator?', true), 'empty' => false)); ?>
	</div>
</div>

	<?php
	echo $this->Form->submit($button, array('class' => 'button'));
	echo $this->Form->end();
} ?>