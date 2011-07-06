
<div class="navigation">
	<?php $links = array();

	if ($this->Common->user()) {
		$links[] = $this->Html->link(__d('forum', 'View New Posts', true), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false));
		$links[] = $this->Html->link(__d('forum', 'My Profile', true), array('controller' => 'users', 'action' => 'profile', $this->Common->user('id'), 'admin' => false));
		$links[] = $this->Html->link(__d('forum', 'Edit Profile', true), array('controller' => 'users', 'action' => 'edit', 'admin' => false));
		$links[] = $this->Html->link(__d('forum', 'Logout', true), $config['routes']['logout']);

	} else {
		$links[] = $this->Html->link(__d('forum', 'Login', true), $config['routes']['login']);
		$links[] = $this->Html->link(__d('forum', 'Sign Up', true), $config['routes']['signup']);

		if (!empty($config['routes']['forgotPass'])) {
			$links[] = $this->Html->link(__d('forum', 'Forgot Password', true), $config['routes']['forgotPass']);
		}
	}

	$links[] = $this->Time->nice(time(), $this->Common->timezone());
	echo implode(' - ', $links); ?>
</div>
