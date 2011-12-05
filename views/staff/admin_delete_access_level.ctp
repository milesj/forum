<?php

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff', true), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Delete Access Level', true), $this->here); ?>

<div class="title">
	<h2><?php __d('forum', 'Delete Access Level'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Before you delete the access level %s, please select which level a user should receive, if they have the level that will be deleted.', true), '<strong>'. $access['AccessLevel']['title'] .'</strong>'); ?></p>

<?php echo $this->Form->create('AccessLevel', array(
	'url' => array('controller' => 'staff', $access['AccessLevel']['id'])
)); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('access_level_id', array('options' => $levels, 'escape' => false, 'label' => __d('forum', 'Move Users To', true)));	?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Delete', true), array('class' => 'button'));
echo $this->Form->end(); ?>