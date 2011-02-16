
<div class="forumHeader">
	<h2><?php __d('forum', 'Report Post'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to report the post (below) in the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.', true), $this->Html->link($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug']))); ?></p>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('Report', array('url' => array('controller' => 'posts', 'action' => 'report', $id))); ?>
<?php echo $this->Form->input('post', array('type' => 'textarea', 'value' => $post['Post']['content'], 'readonly' => 'readonly', 'label' => __d('forum', 'Post', true))); ?>
<?php echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Report', true)); ?>