
<div class="forumHeader">
	<h2><?php __d('forum', 'Edit User'); ?></h2>
</div>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit', 'admin' => true))); ?>
<?php echo $this->Form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $this->Form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $this->Form->input($this->Common->columnMap['status'], array('label' => __d('forum', 'Status', true), 'options' => $this->Common->options(5))); ?>
<?php echo $this->Form->input($this->Common->columnMap['totalPosts'], array('label' => __d('forum', 'Total Posts', true))); ?>
<?php echo $this->Form->input($this->Common->columnMap['totalTopics'], array('label' => __d('forum', 'Total Topics', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Update', true)); ?>