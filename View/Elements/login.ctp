
<?php if (!$user) { ?>

	<div class="login">
		<?php
		echo $this->Form->create('User', array('url' => $config['routes']['login']));
		echo $this->Form->input($config['userMap']['username'], array('error' => false, 'label' => __d('forum', 'Username') . ': '));
		echo $this->Form->input($config['userMap']['password'], array('error' => false, 'label' => __d('forum', 'Password') . ': '));
		echo $this->Form->input('auto_login', array('type' => 'checkbox', 'error' => false, 'label' => false, 'after' => ' ' . __d('forum', 'Remember Me?')));
		echo $this->Form->submit(__d('forum', 'Login'), array('class' => 'buttonSmall', 'div' => false));
		echo $this->Form->end(); ?>
	</div>

<?php } ?>