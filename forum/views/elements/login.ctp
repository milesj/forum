
<?php echo $form->create('User', array('url' => array('plugin' => 'forum', 'controller' => 'users', 'action' => 'login'))); ?>
<table cellpadding="5" cellspacing="0">
<tr>
	<td><?php echo $form->input('username', array('div' => false, 'error' => false, 'label' => __d('forum', 'Username', true) .': ')); ?></td>
	<td><?php echo $form->input('password', array('div' => false, 'error' => false, 'label' => __d('forum', 'Password', true) .': ')); ?></td>
    <td><?php echo $form->input('auto_login', array('type' => 'checkbox', 'div' => false, 'error' => false, 'label' => false, 'after' => ' '. __d('forum', 'Remember Me?', true))); ?></td>
    <td><?php echo $form->submit(__d('forum', 'Login', true), array('div' => false)); ?></td>
</tr>
</table>
<?php echo $form->end(); ?>