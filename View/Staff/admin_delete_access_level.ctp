<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Delete Access Level'), $this->here); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Delete Access Level'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Before you delete the access level %s, please select which level a user should receive, if they have the level that will be deleted.'), '<strong>' . $access['AccessLevel']['title'] . '</strong>'); ?></p>

<?php echo $this->Form->create('AccessLevel'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Move Users To')));	?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Delete'), array('class' => 'button'));
echo $this->Form->end(); ?>