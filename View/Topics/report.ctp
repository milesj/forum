<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Report Topic'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to report the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.'), '<strong>' . $this->Html->link($topic['Topic']['title'], array('action' => 'view', $topic['Topic']['slug'])) . '</strong>'); ?></p>

<?php echo $this->Form->create('Report'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'button'));
echo $this->Form->end(); ?>