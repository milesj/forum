
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

<div class="forumHeader">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php __d('forum', 'When applying a view access, it states that all users with that access level and above will be able to view the forum and all its child forum categories. However, the restricted access will limit only users with that access level to view the forum.'); ?></p>

<?php 
echo $this->Form->create('Forum', array('url' => array('controller' => 'categories', 'action' => $action, 'admin' => true)));
echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));
echo $this->Form->input('status', array('options' => $this->Cupcake->options(3), 'label' => __d('forum', 'Status', true)));
echo $this->Form->input('orderNo', array('style' => 'width: 50px', 'label' => __d('forum', 'Order No', true)));
echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Restrict Access To', true)));
echo $this->Form->input('accessView', array('options' => $this->Cupcake->options(4, null, true), 'label' => __d('forum', 'View Access', true)));
echo $this->Form->end($button); ?>