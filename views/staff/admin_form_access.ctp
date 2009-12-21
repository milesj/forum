
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

<h2><?php echo $title; ?></h2>

<p><?php printf(__d('forum', 'To find the users ID, you can search for them on the %s.', true), $html->link(__d('forum', 'Users listing', true), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>

<?php 
echo $form->create('Access', array('url' => array('controller' => 'staff', 'action' => $action, 'admin' => true)));
echo $form->input('user_id', array('style' => 'width: 50px', 'label' => __d('forum', 'User ID', true)));
echo $form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Access Level', true), 'empty' => false));
echo $form->end($button); ?>