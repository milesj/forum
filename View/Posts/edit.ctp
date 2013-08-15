<?php
if (!empty($post['Forum']['Parent']['slug'])) {
	$this->Breadcrumb->add($post['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($post['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['slug']));
$this->Breadcrumb->add($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug']));
$this->Breadcrumb->add(__d('forum', 'Edit Post'), array('action' => 'edit', $post['Topic']['slug'])); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Edit Post'); ?></h2>
</div>

<div class="container">
	<?php
	echo $this->Form->create('Post');
	echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => __d('forum', 'Content')));
	echo $this->element('decoda', array('id' => 'PostContent'));
	echo $this->Form->submit(__d('forum', 'Update Post'), array('class' => 'button success large'));
	echo $this->Form->end(); ?>
</div>