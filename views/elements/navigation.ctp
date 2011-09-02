
<div class="navigation">
	<div>
		<?php echo $this->Time->nice(time(), $this->Common->timezone()); ?>
	</div>

	<?php $links = array();

	if ($user) {
		$links[] = $this->Html->link(__d('forum', 'Logout', true), $config['routes']['logout']);
		$links[] = $this->Html->link(__d('forum', 'View New Posts', true), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false));
		$links[] = $this->Html->link(__d('forum', 'Edit Profile', true), array('controller' => 'users', 'action' => 'edit', 'admin' => false));
		
	} else {
		if (!empty($config['routes']['forgotPass'])) {
			$links[] = $this->Html->link(__d('forum', 'Forgot Password', true), $config['routes']['forgotPass']);
		}
		
		$links[] = $this->Html->link(__d('forum', 'Login', true), $config['routes']['login']);
		
		if (!empty($config['routes']['signup'])) {
			$links[] = $this->Html->link(__d('forum', 'Sign Up', true), $config['routes']['signup']);
		}
	} 
	
	foreach ($links as $link) { ?>
	
		<div>
			<?php echo $link; ?>
		</div>
	
	<?php }
	
	if ($user) { ?>

		<div>
			<?php echo sprintf(__d('forum', 'Welcome %s', true), $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $user['User']['id'], 'admin' => false))); ?>
		</div>
	
	<?php } ?>
	
	<span class="clear"></span>
</div>
