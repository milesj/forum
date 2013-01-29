<?php

if ($method === 'add') {
	$action = 'add_access';
	$button = __d('forum', 'Save');
	$title = __d('forum', 'Add Access');
} else {
	$action = 'edit_access';
	$button = __d('forum', 'Update');
	$title = __d('forum', 'Edit Access');
}

$this->Breadcrumb->add(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Breadcrumb->add(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index'));
$this->Breadcrumb->add($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<?php if ($method === 'add') { ?>
	<p><?php printf(__d('forum', 'To find the users ID, you can search for them in the %s.'), $this->Html->link(strtolower(__d('forum', 'Users listing')), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>
<?php }

echo $this->Form->create('Access'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		if ($method === 'add') {
			echo $this->Form->input('foreign_key', array('type' => 'text', 'class' => 'numeric', 'label' => __d('forum', 'User ID')));
		} else {
			echo $this->Form->input('User.' . $config['userMap']['username'], array('type' => 'text',  'label' => __d('forum', 'User'), 'readonly' => true));
		}
		echo $this->Form->input('parent_id', array('options' => $this->Forum->options('accessGroups'), 'label' => __d('forum', 'Access Level'), 'empty' => false)); ?>
	</div>
</div>

<?php
echo $this->Form->submit($button, array('class' => 'button'));
echo $this->Form->end(); ?>