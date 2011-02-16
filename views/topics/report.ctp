
<div class="forumHeader">
	<h2><?php __d('forum', 'Report Topic'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to report the topic %s ? If so, please add a comment as to why you are reporting it, 255 max characters.', true), $this->Html->link($topic['Topic']['title'], array('action' => 'view', $topic['Topic']['slug']))); ?></p>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('Report', array('url' => array('controller' => 'topics', 'action' => 'report', $id))); ?>
<?php echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Report', true)); ?>