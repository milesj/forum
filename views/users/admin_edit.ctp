<?php 

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'index'));
$this->Html->addCrumb($profile['User'][$config['userMap']['username']], $this->here); ?>

<div class="controls float-right">
	<?php if ($profile['User'][$config['userMap']['status']] != $config['statusMap']['active']) {
		echo $this->Html->link(__d('forum', 'Activate', true), array('action' => 'status', $profile['User']['id'], 'active'), array('class' => 'button'));
	} else {
		echo $this->Html->link(__d('forum', 'Ban', true), array('action' => 'status', $profile['User']['id'], 'banned'), array('class' => 'button'));
	} ?>
</div>

<div class="title">
	<h2><?php __d('forum', 'Edit User'); ?></h2>
</div>

<?php echo $this->Form->create('Profile', array(
	'url' => array('controller' => 'users')
)); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('User.' . $config['userMap']['username'], array('label' => __d('forum', 'Username', true), 'readonly' => true));
		echo $this->Form->input('User.' . $config['userMap']['email'], array('label' => __d('forum', 'Email', true), 'readonly' => true));
		echo $this->Form->input('User.' . $config['userMap']['status'], array('label' => __d('forum', 'Status', true), 'readonly' => true));
		echo $this->Form->input('totalPosts', array('label' => __d('forum', 'Total Posts', true), 'class' => 'numeric'));
		echo $this->Form->input('totalTopics', array('label' => __d('forum', 'Total Topics', true), 'class' => 'numeric'));
		echo $this->Form->input('locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language', true)));
		echo $this->Form->input('timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone', true)));
		echo $this->Form->input('signature', array('type' => 'textarea', 'label' => __d('forum', 'Signature', true))); ?>
	</div>
</div>

<?php 
echo $this->Form->submit(__d('forum', 'Update', true), array('class' => 'button'));
echo $this->Form->end(); ?>