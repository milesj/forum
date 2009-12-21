
<?php // Switch
if ($method == 'add') {
	$action = 'add_access_level';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Access Level', true);
} else {
	$action = 'edit_access_level';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Access Level', true);
} ?>

<h2><?php echo $title; ?></h2>

<?php if ($method == 'edit' && $id <= 4) { ?>
<p><?php __d('forum', 'You are unable to edit core levels, they are restricted.'); ?></p>

<?php } else {
	echo $form->create('AccessLevel', array('url' => array('controller' => 'staff', 'action' => $action, 'admin' => true)));
	echo $form->input('title', array('label' => __d('forum', 'Title', true)));
	echo $form->input('level', array('options' => $cupcake->options(4), 'label' => __d('forum', 'Access Level', true), 'empty' => false));
	echo $form->input('is_super', array('options' => $cupcake->options(1), 'label' => __d('forum', 'Is Super Moderator?', true), 'empty' => false));
	echo $form->input('is_admin', array('options' => $cupcake->options(1), 'label' => __d('forum', 'Is Administrator?', true), 'empty' => false));
	echo $form->end($button);
} ?>