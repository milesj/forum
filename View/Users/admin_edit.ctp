<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Html->addCrumb($profile['User'][$config['userMap']['username']], $this->here); ?>

<div class="controls float-right">
	<?php if ($profile['User'][$config['userMap']['status']] != $config['statusMap']['active']) {
		echo $this->Html->link(__d('forum', 'Activate'), array('action' => 'status', $profile['User']['id'], 'active'), array('class' => 'button'));
	} else {
		echo $this->Html->link(__d('forum', 'Ban'), array('action' => 'status', $profile['User']['id'], 'banned'), array('class' => 'button'));
	} ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'Edit User'); ?></h2>
</div>

<?php echo $this->Form->create('Profile'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('User.' . $config['userMap']['username'], array('label' => __d('forum', 'Username'), 'readonly' => true));
		echo $this->Form->input('User.' . $config['userMap']['email'], array('label' => __d('forum', 'Email'), 'readonly' => true));
		echo $this->Form->input('User.' . $config['userMap']['status'], array('label' => __d('forum', 'Status'), 'readonly' => true));
		echo $this->Form->input('totalPosts', array('label' => __d('forum', 'Total Posts'), 'class' => 'numeric'));
		echo $this->Form->input('totalTopics', array('label' => __d('forum', 'Total Topics'), 'class' => 'numeric'));
		echo $this->Form->input('locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language')));
		echo $this->Form->input('timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone')));
		echo $this->Form->input('signature', array('type' => 'textarea', 'label' => __d('forum', 'Signature'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Update'), array('class' => 'button'));
echo $this->Form->end(); ?>