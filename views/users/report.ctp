
<div class="forumHeader">
	<h2><?php __d('forum', 'Report User'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to report the user %s ? If so, please add a comment as to why you are reporting this user, and please be descriptive. Are they spamming, trolling, flaming, etc. 255 max characters.', true), $this->Html->link($user['User']['username'], array('action' => 'profile', $user['User']['id']))); ?></p>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('Report', array('url' => array('controller' => 'users', 'action' => 'report', $id))); ?>
<?php echo $this->Form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Report', true)); ?>