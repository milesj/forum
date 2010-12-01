
<div id="navigation">
    <span class="fr">
        <?php // User links
        $links = array();

        if ($this->Common->user()) {
            $links[] = $this->Html->link(__d('forum', 'View New Posts', true), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false));
            $links[] = $this->Html->link(__d('forum', 'My Profile', true), array('controller' => 'users', 'action' => 'profile', $this->Common->user('id'), 'admin' => false));
            $links[] = $this->Html->link(__d('forum', 'Edit Profile', true), array('controller' => 'users', 'action' => 'edit', 'admin' => false));
            $links[] = $this->Html->link(__d('forum', 'Logout', true), $plugin['routes']['logout']);

		} else {
            $links[] = $this->Html->link(__d('forum', 'Login', true), $plugin['routes']['login']);
            $links[] = $this->Html->link(__d('forum', 'Sign Up', true), $plugin['routes']['signup']);

			if (!empty($plugin['routes']['forgotPass'])) {
				$links[] = $this->Html->link(__d('forum', 'Forgot Password', true), $plugin['routes']['forgotPass']);
			}
        }
        
        $links[] = $this->Time->nice(time(), $this->Common->timezone());
        echo implode(' - ', $links); ?>
    </span>
    
    <?php echo $this->Html->getCrumbs(' &raquo; '); ?>
    <span class="clear"><!-- --></span>
</div>
