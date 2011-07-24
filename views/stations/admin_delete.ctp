<?php

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Forums', true), array('controller' => 'stations', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Delete Forum', true), $this->here); ?>

<div class="title">
	<h2><?php __d('forum', 'Delete Forum'); ?></h2>
</div>

<?php if (empty($subForums)) { ?>

<p><?php __d('forum', 'You may not delete this forum, you must have at least one forum active.'); ?></p>

<?php } else { ?>
	
<p><?php printf(__d('forum', 'Before you delete the %s forum, please select which forum all topics and sub-forums should be moved to.', true), '<strong>'. $forum['Forum']['title'] .'</strong>'); ?></p>

<?php echo $this->Form->create('Forum', array('url' => $this->here)); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('move_topics', array('options' => $topicForums, 'escape' => false, 'label' => __d('forum', 'Move Topics To', true)));
		echo $this->Form->input('move_forums', array('options' => $subForums, 'escape' => false, 'label' => __d('forum', 'Move Sub-forums To', true))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Delete', true), array('class' => 'button'));
echo $this->Form->end();
}