
<div class="navigation">
	<div>
		<?php echo $this->Time->nice(time(), $this->Forum->timezone()); ?>
	</div>

	<?php $links = array();

	if ($user) {
		$links[] = $this->Html->link(__d('forum', 'Logout'), $userRoutes['logout']);
		$links[] = $this->Html->link(__d('forum', 'View New Posts'), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false));
	} else {
		if (!empty($userRoutes['forgotPass'])) {
			$links[] = $this->Html->link(__d('forum', 'Forgot Password'), $userRoutes['forgotPass']);
		}

		$links[] = $this->Html->link(__d('forum', 'Login'), $userRoutes['login']);

		if (!empty($userRoutes['signup'])) {
			$links[] = $this->Html->link(__d('forum', 'Sign Up'), $userRoutes['signup']);
		}
	}

	foreach ($links as $link) { ?>

		<div>
			<?php echo $link; ?>
		</div>

	<?php }

	if ($user) { ?>

		<div>
			<?php echo sprintf(__d('forum', 'Welcome %s'), $this->Html->link($user[$userFields['username']], $this->Forum->profileUrl($user))); ?>
		</div>

	<?php } ?>

	<span class="clear"></span>
</div>
