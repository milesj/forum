
<h2><?php __d('forum', 'Edit User'); ?></h2>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit', 'admin' => true))); ?>
<?php echo $form->input('username', array('label' => __d('forum', 'Username', true))); ?>
<?php echo $form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $form->input('status', array('label' => __d('forum', 'Status', true), 'options' => $cupcake->options(5))); ?>
<?php echo $form->input('totalPosts', array('label' => __d('forum', 'Total Posts', true))); ?>
<?php echo $form->input('totalTopics', array('label' => __d('forum', 'Total Topics', true))); ?>
<?php echo $form->end(__d('forum', 'Update', true)); ?>