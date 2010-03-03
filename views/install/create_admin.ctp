
<div class="forumHeader">
	<h2>Create Administrator</h2>
</div>

<?php if ($granted) { ?>
<div class="successBox">
	<b>Success!</b> Your user has succesfully been granted administrator access.
</div>
<?php } ?>

<p>A forum needs an administrator to configure the site and manage the content.
The form below quickly allows you to add an administrator into the system instead of having to do it manually.</p>

<?php if ($granted) { ?>
<div class="submit">
	<?php echo $form->button('Visit Forum', array('onclick' => "goTo('". Router::url(array('controller' => 'home', 'action' => 'index', 'plugin' => 'forum')) ."');")); ?>
</div>

<?php } else {
	if ($total >= 1) { ?>
		<p>Please enter the user ID of the user you want to give administrator access.</p>

		<?php // Form
		echo $form->create('User', array('url' => array('controller' => 'install', 'action' => 'create_admin')));
		echo $form->input('user_id', array('label' => 'User ID'));
		echo $form->end('Grant Access');
		
	} else { ?>
		<p>Since there are no users yet in the system, we must create a new user.</p>

		<?php // Form
		echo $form->create('User', array('url' => array('controller' => 'install', 'action' => 'create_admin')));
		echo $form->input('username', array('label' => __d('forum', 'Username', true)));
		echo $form->input('email', array('label' => __d('forum', 'Email', true)));
		echo $form->input('newPassword', array('type' => 'password', 'label' => __d('forum', 'Password', true)));
		echo $form->input('confirmPassword', array('type' => 'password', 'label' => __d('forum', 'Confirm Password', true)));
		echo $form->end('Create User');
	}
} ?>