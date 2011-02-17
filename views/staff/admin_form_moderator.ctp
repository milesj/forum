
<?php // Switch
if ($method == 'add') {
	$action = 'add_moderator';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Moderator', true);
} else {
	$action = 'edit_moderator';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Moderator', true);
} ?>

<div class="forumHeader">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php printf(__d('forum', 'To find the users ID, you can search for them on the %s.', true), $this->Html->link(__d('forum', 'Users listing', true), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>

<?php 
echo $this->Form->create('Moderator', array('url' => array('controller' => 'staff', 'action' => $action, 'admin' => true)));
echo $this->Form->input('user_id', array('style' => 'width: 50px', 'label' => __d('forum', 'User ID', true), 'type' => 'text'));
echo $this->Form->input('forum_category_id', array('options' => $forums, 'label' => __d('forum', 'Forum Category', true), 'empty' => true, 'escape' => false));
echo $this->Form->end($button); ?>