
<?php // Switch
if ($method == 'add') {
	$action = 'add_forum';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Forum', true);
} else {
	$action = 'edit_forum';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Forum', true);
}

array_unshift($levels, '-- '. __d('forum', 'None', true) .' --'); ?>

<h2><?php echo $title; ?></h2>

<p><?php __d('forum', 'When applying a view access, it states that all users with that access level and above will be able to view the forum and all its child forum categories. However, the restricted access will limit only users with that access level to view the forum.'); ?></p>

<?php 
echo $form->create('Forum', array('url' => array('controller' => 'categories', 'action' => $action, 'admin' => true)));
echo $form->input('title', array('label' => __d('forum', 'Title', true)));
echo $form->input('status', array('options' => $cupcake->options(3), 'label' => __d('forum', 'Status', true)));
echo $form->input('orderNo', array('style' => 'width: 50px', 'label' => __d('forum', 'Order No', true)));
echo $form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Restrict Access To', true)));
echo $form->input('accessView', array('options' => $cupcake->options(4, null, true), 'label' => __d('forum', 'View Access', true)));
echo $form->end($button); ?>