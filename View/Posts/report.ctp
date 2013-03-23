<?php

if (!empty($post['Forum']['Parent']['slug'])) {
	$this->Breadcrumb->add($post['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($post['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['slug']));
$this->Breadcrumb->add($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug']));
$this->Breadcrumb->add(__d('forum', 'Report Post'), array('action' => 'report', $post['Post']['id'])); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Report Post'); ?></h2>
</div>

<p>
	<?php printf(__d('forum', 'Are you sure you want to report the post (below) in the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.'),
		'<strong>' . $this->Html->link($post['Topic']['title'], array('controller' => 'forum', 'action' => 'jump', $post['Topic']['id'], $post['Post']['id'])) . '</strong>'); ?>
</p>

<?php echo $this->Form->create('Report'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('post', array('type' => 'textarea', 'readonly' => 'readonly', 'escape' => false, 'label' => __d('forum', 'Post')));
		echo $this->Form->input('type', array('options' => $this->Utility->enum('Admin.ItemReport', 'type')));
		echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment'))); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'button'));
echo $this->Form->end(); ?>