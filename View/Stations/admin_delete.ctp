<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Forums'), array('controller' => 'stations', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Delete Forum'), $this->here); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Delete Forum'); ?></h2>
</div>

<?php if (empty($subForums)) { ?>

<p><?php echo __d('forum', 'You may not delete this forum, you must have at least one forum active.'); ?></p>

<?php } else { ?>

<p><?php printf(__d('forum', 'Before you delete the %s forum, please select which forum all topics and sub-forums should be moved to.'), '<strong>' . $forum['Forum']['title'] . '</strong>'); ?></p>

<?php echo $this->Form->create('Forum'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('move_topics', array('options' => $topicForums, 'label' => __d('forum', 'Move Topics To')));
		echo $this->Form->input('move_forums', array('options' => $subForums, 'label' => __d('forum', 'Move Sub-forums To'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Delete'), array('class' => 'button'));
echo $this->Form->end();
}