<?php

if ($method == 'add') {
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Forum', true);
} else {
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Forum', true);
} 

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Forums', true), array('controller' => 'stations', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php __d('forum', 'When applying a read, post, reply or poll access, it states that all users with that access level and above will be able to commit the respective action. It does not mean only that type of access can view the forum category. However, the restricted access will limit only users with that access level to view the forum category.'); ?></p>

<?php echo $this->Form->create('Forum', array('url' => $this->here)); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));
		echo $this->Form->input('status', array('options' => $this->Common->options('forumStatus'), 'label' => __d('forum', 'Status', true)));
		echo $this->Form->input('orderNo', array('style' => 'width: 50px', 'label' => __d('forum', 'Order No', true))); ?>

		<div class="inputDivider"><?php __d('forum', 'The fields below only apply to non-top level forums.'); ?></div>

		<?php
		echo $this->Form->input('description', array('type' => 'textarea', 'label' => __d('forum', 'Description', true)));
		echo $this->Form->input('forum_id', array('options' => $forums, 'label' => __d('forum', 'Forum', true), 'escape' => false, 'empty' => '-- '. __d('forum', 'None', true) .' --'));
		echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Restrict Access To', true), 'empty' => '-- '. __d('forum', 'None', true) .' --'));
		echo $this->Form->input('accessRead', array('options' => $this->Common->options('access', null, true), 'label' => __d('forum', 'Read Access', true)));
		echo $this->Form->input('accessPost', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Post Access', true)));
		echo $this->Form->input('accessReply', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Reply Access', true)));
		echo $this->Form->input('accessPoll', array('options' => $this->Common->options('access'), 'label' => __d('forum', 'Poll Access', true)));
		echo $this->Form->input('settingPostCount', array('options' => $this->Common->options(), 'label' => __d('forum', 'Increase Users Post/Topic Count', true)));
		echo $this->Form->input('settingAutoLock', array('options' => $this->Common->options(), 'label' => __d('forum', 'Auto-Lock Inactive Topics', true))); ?>
	</div>
</div>
	
<?php
echo $this->Form->submit($button, array('class' => 'button'));
echo $this->Form->end(); ?>