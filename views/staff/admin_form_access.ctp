<?php 

if ($method == 'add') {
	$action = 'add_access';
	$button = __d('forum', 'Save', true);
	$title = __d('forum', 'Add Access', true);
} else {
	$action = 'edit_access';
	$button = __d('forum', 'Update', true);
	$title = __d('forum', 'Edit Access', true);
}

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff', true), array('controller' => 'staff', 'action' => 'index'));
$this->Html->addCrumb($title, $this->here); ?>

<div class="title">
	<h2><?php echo $title; ?></h2>
</div>

<?php if ($method == 'add') { ?>
	<p><?php printf(__d('forum', 'To find the users ID, you can search for them in the %s.', true), $this->Html->link(strtolower(__d('forum', 'Users listing', true)), array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?></p>
<?php }

echo $this->Form->create('Access', array(
	'url' => array('controller' => 'staff')
)); ?>

<div class="container">
	<div class="containerContent">
		<?php
		if ($method == 'add') {
			echo $this->Form->input('user_id', array('type' => 'text', 'class' => 'numeric', 'label' => __d('forum', 'User ID', true)));
		} else {
			echo $this->Form->input('User.'. $config['userMap']['username'], array('type' => 'text',  'label' => __d('forum', 'User', true), 'readonly' => true));
		}
		echo $this->Form->input('access_level_id', array('options' => $levels, 'label' => __d('forum', 'Access Level', true), 'empty' => false)); ?>
	</div>
</div>

<?php 
echo $this->Form->submit($button, array('class' => 'button'));
echo $this->Form->end(); ?>