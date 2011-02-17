
<?php // Switch
if ($method == 'add') {
	$action = 'add_access';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Access', true);
} else {
	$action = 'edit_access';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Access', true);
} ?>

<div class="forumHeader">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php printf(__d('forum', 'To find the users ID, you can search for them on the %s.', true), $this->Html->link(__d('forum', 'Users listing', true), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>

<?php 
echo $this->Form->create('Access', array('url' => array('controller' => 'staff', 'action' => $action, 'admin' => true)));
echo $this->Form->input('user_id', array('style' => 'width: 50px', 'label' => __d('forum', 'User ID', true), 'type' => 'text'));
echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Access Level', true), 'empty' => false));
echo $this->Form->end($button); ?>