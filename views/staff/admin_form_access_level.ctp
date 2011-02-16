
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

<div class="forumHeader">
	<h2><?php echo $title; ?></h2>
</div>

<?php if ($method == 'edit' && $id <= 4) { ?>
<p><?php __d('forum', 'You are unable to edit core levels, they are restricted.'); ?></p>

<?php } else {
	echo $this->Form->create('AccessLevel', array('url' => array('controller' => 'staff', 'action' => $action, 'admin' => true)));
	echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));
	echo $this->Form->input('level', array('options' => $this->Cupcake->options(4), 'label' => __d('forum', 'Access Level', true), 'empty' => false));
	echo $this->Form->input('is_super', array('options' => $this->Cupcake->options(1), 'label' => __d('forum', 'Is Super Moderator?', true), 'empty' => false));
	echo $this->Form->input('is_admin', array('options' => $this->Cupcake->options(1), 'label' => __d('forum', 'Is Administrator?', true), 'empty' => false));
	echo $this->Form->end($button);
} ?>