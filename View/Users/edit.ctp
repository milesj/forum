<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Edit Profile'), array('controller' => 'users', 'action' => 'edit')); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Edit Profile'); ?></h2>
</div>

<?php echo $this->Form->create('Profile'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language')));
		echo $this->Form->input('timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone')));
		echo $this->Form->input('signature', array('type' => 'textarea', 'rows' => 5, 'label' => __d('forum', 'Signature')));
		echo $this->element('markitup', array('textarea' => 'ProfileSignature')); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Update Account'), array('class' => 'button'));
echo $this->Form->end(); ?>
