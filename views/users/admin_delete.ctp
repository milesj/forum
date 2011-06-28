
<div class="forumHeader">
	<h2><?php __d('forum', 'Delete User'); ?></h2>
</div>

<p>
	<?php printf(__d('forum', 'Are you sure you want to delete the user %s ? Once deleted, all the users topics, posts, moderator positions, access levels and any other associations will permanently be deleted from the system.', true), '<strong>'. $profile['User'][$config['userMap']['username']] .'</strong>'); ?><br />
	<?php __d('forum', 'However, this *does not* delete the user, just its forum profile!'); ?>
</p>

<?php 
echo $this->Form->create('User', array('url' => $this->here));
echo $this->Form->input('delete', array('type' => 'hidden', 'value' => 'yes'));
echo $this->Form->end(__d('forum', 'Yes, Delete', true)); ?>