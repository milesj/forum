<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php __d('forum', 'Report Topic'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to report the topic %s ? If so, please add a comment as to why you are reporting it, 255 max characters.', true), $this->Html->link($topic['Topic']['title'], array('action' => 'view', $topic['Topic']['slug']))); ?></p>

<?php echo $this->Form->create('Report', array('url' => $this->here)); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
	</div>
</div>

<?php echo $this->Form->end(__d('forum', 'Report', true)); ?>