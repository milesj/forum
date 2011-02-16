
<div id="navigation">
    <span class="fr">
        <?php // User links
        $links = array();
        if ($this->Cupcake->user()) {
            $links[] = $this->Html->link(__d('forum', 'View New Posts', true), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false));
            $links[] = $this->Html->link(__d('forum', 'My Profile', true), array('controller' => 'users', 'action' => 'profile', $this->Cupcake->user('id'), 'admin' => false));
            $links[] = $this->Html->link(__d('forum', 'Edit Profile', true), array('controller' => 'users', 'action' => 'edit', 'admin' => false));
            $links[] = $this->Html->link(__d('forum', 'Logout', true), array('controller' => 'users', 'action' => 'logout', 'admin' => false));
        } else {
            $links[] = $this->Html->link(__d('forum', 'Login', true), array('controller' => 'users', 'action' => 'login'));
            $links[] = $this->Html->link(__d('forum', 'Sign Up', true), array('controller' => 'users', 'action' => 'signup'));
            $links[] = $this->Html->link(__d('forum', 'Forgot Password', true), array('controller' => 'users', 'action' => 'forgot'));
        }
        
        $links[] = $this->Time->nice(time(), $this->Cupcake->timezone());
        echo implode(' - ', $links); ?>
    </span>
    
    <?php echo $this->Html->getCrumbs(' &raquo; '); ?>
    <span class="clear"><!-- --></span>
</div>
