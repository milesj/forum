<?php

if ($method === 'add') {
	$button = __d('forum', 'Save');
	$title = __d('forum', 'Add Forum');
} else {
	$button = __d('forum', 'Update');
	$title = __d('forum', 'Edit Forum');
}

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Forums'), array('controller' => 'stations', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<?php echo $this->Form->create('Forum'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('title', array('label' => __d('forum', 'Title')));
		echo $this->Form->input('status', array('options' => $this->Common->options('forumStatus'), 'label' => __d('forum', 'Status')));
		echo $this->Form->input('orderNo', array('style' => 'width: 50px', 'maxlength' => 2, 'label' => __d('forum', 'Order No'))); ?>

		<div class="inputDivider"><?php echo __d('forum', 'The fields below apply to child level forums.'); ?></div>

		<?php
		echo $this->Form->input('description', array('type' => 'textarea', 'label' => __d('forum', 'Description')));
		echo $this->Form->input('forum_id', array('options' => $forums, 'label' => __d('forum', 'Forum'), 'empty' => '-- ' . __d('forum', 'None') . ' --'));
		echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Restrict Access To'), 'empty' => '-- ' . __d('forum', 'None') . ' --'));
		echo $this->Form->input('accessRead', array('options' => $this->Common->options('access', null, true), 'label' => __d('forum', 'Read Access')));
		echo $this->Form->input('accessPost', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Post Access')));
		echo $this->Form->input('accessReply', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Reply Access')));
		echo $this->Form->input('accessPoll', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Poll Access')));
		echo $this->Form->input('settingPostCount', array('options' => $this->Common->options(), 'label' => __d('forum', 'Increase Users Post/Topic Count')));
		echo $this->Form->input('settingAutoLock', array('options' => $this->Common->options(), 'label' => __d('forum', 'Auto-Lock Inactive Topics'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit($button, array('class' => 'button'));
echo $this->Form->end(); ?>