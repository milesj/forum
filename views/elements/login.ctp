
<?php if (!$this->Common->user()) { ?>

	<div class="login">
		<?php echo $this->Form->create('User', array('url' => $config['routes']['login'])); ?>

		<table cellpadding="5" cellspacing="0">
		<tr>
			<td><?php echo $this->Form->input($config['userMap']['username'], array('div' => false, 'error' => false, 'label' => __d('forum', 'Username', true) .': ')); ?></td>
			<td><?php echo $this->Form->input($config['userMap']['password'], array('div' => false, 'error' => false, 'label' => __d('forum', 'Password', true) .': ')); ?></td>
			<td><?php echo $this->Form->input('auto_login', array('type' => 'checkbox', 'div' => false, 'error' => false, 'label' => false, 'after' => ' '. __d('forum', 'Remember Me?', true))); ?></td>
			<td><?php echo $this->Form->submit(__d('forum', 'Login', true), array('div' => false)); ?></td>
		</tr>
		</table>

		<?php echo $this->Form->end(); ?>
	</div>

<?php } ?>