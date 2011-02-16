
<?php // Switch
if ($method == 'add') {
	$action = 'add_category';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Forum Category', true);
} else {
	$action = 'edit_category';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Forum Category', true);
}

array_unshift($levels, '-- '. __d('forum', 'None', true) .' --'); ?>

<div class="forumHeader">
	<h2><?php echo $title; ?></h2>
</div>

<p><?php __d('forum', 'When applying a read, post, reply or poll access, it states that all users with that access level and above will be able to commit the respective action. It does not mean only that type of access can view the forum category. However, the restricted access will limit only users with that access level to view the forum category.'); ?></p>

<?php 
echo $this->Form->create('ForumCategory', array('url' => array('controller' => 'categories', 'action' => $action, 'admin' => true)));
echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));
echo $this->Form->input('description', array('type' => 'textarea', 'label' => __d('forum', 'Description', true)));
echo $this->Form->input('forum_id', array('options' => $forums, 'label' => __d('forum', 'Forum', true)));
if (!empty($categories)) {
	echo $this->Form->input('parent_id', array('options' => $categories, 'label' => __d('forum', 'Parent Category', true), 'empty' => true));
}
echo $this->Form->input('status', array('options' => $this->Cupcake->options(2), 'label' => __d('forum', 'Status', true)));
echo $this->Form->input('orderNo', array('style' => 'width: 50px', 'label' => __d('forum', 'Order No', true)));
echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Restrict Access To', true)));
echo $this->Form->input('accessRead', array('options' => $this->Cupcake->options(4, null, true), 'label' => __d('forum', 'Read Access', true)));
echo $this->Form->input('accessPost', array('options' => $this->Cupcake->options(4), 'label' => __d('forum', 'Post Access', true)));
echo $this->Form->input('accessReply', array('options' => $this->Cupcake->options(4), 'label' => __d('forum', 'Reply Access', true)));
echo $this->Form->input('accessPoll', array('options' => $this->Cupcake->options(4), 'label' => __d('forum', 'Poll Access', true)));
echo $this->Form->input('settingPostCount', array('options' => $this->Cupcake->options(), 'label' => __d('forum', 'Increase Users Post/Topic Count', true)));
echo $this->Form->input('settingAutoLock', array('options' => $this->Cupcake->options(), 'label' => __d('forum', 'Auto-Lock Inactive Topics', true)));
echo $this->Form->end($button); ?>