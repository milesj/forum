<?php

$this->Breadcrumb->add(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Breadcrumb->add(__d('forum', 'Settings'), array('controller' => 'forum', 'action' => 'settings')); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Settings'); ?></h2>
</div>

<?php echo $this->Form->create('Setting'); ?>

<div class="container">
	<div class="containerContent">
		Settings are no longer managed within the administration panel.<br>
		You can modify the settings with Configure::write('Forum.settings') in your application's bootstrap.<br>
		The database settings will continue to work until version <b>3.4.0</b>, where it will be completely removed.
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Update'), array('class' => 'button'));
echo $this->Form->end(); ?>