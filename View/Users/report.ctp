<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Report User'), $this->here); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Report User'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to report the user %s? If so, please add a comment as to why you are reporting this user, and please be descriptive. Are they spamming, trolling, flaming, etc. 255 max characters.'), '<strong>' . $this->Html->link($profile['User'][$config['userMap']['username']], array('action' => 'profile', $profile['User']['id'])) . '</strong>'); ?></p>

<?php echo $this->Form->create('Report'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'button'));
echo $this->Form->end(); ?>