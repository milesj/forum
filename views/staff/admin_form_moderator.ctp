
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

<h2><?php echo $title; ?></h2>

<p><?php printf(__d('forum', 'To find the users ID, you can search for them on the %s.', true), $html->link(__d('forum', 'Users listing', true), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>

<?php 
echo $form->create('Moderator', array('url' => array('controller' => 'staff', 'action' => $action, 'admin' => true)));
echo $form->input('user_id', array('style' => 'width: 50px', 'label' => __d('forum', 'User ID', true)));
echo $form->input('forum_category_id', array('options' => $forums, 'label' => __d('forum', 'Forum Category', true), 'empty' => true, 'escape' => false));
echo $form->end($button); ?>