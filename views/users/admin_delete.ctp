
<div class="forumHeader">
	<h2><?php __d('forum', 'Delete User'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Are you sure you want to delete the user %s ? Once deleted, all the users topics, posts, moderator positions, access levels and any other associations will permanently be deleted from the system.', true), '<strong>'. $user['User']['username'] .'</strong>'); ?></p>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'delete', $id, 'admin' => true)));
echo $this->Form->input('delete', array('type' => 'hidden', 'value' => 'yes'));
echo $this->Form->end(__d('forum', 'Yes, Delete', true)); ?>