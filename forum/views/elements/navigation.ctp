
<div id="navigation">
    <span class="fr">
        <?php // User links
        $links = array();
        if ($cupcake->user()) {
            $links[] = $html->link(__d('forum', 'View New Posts', true), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false));
            $links[] = $html->link(__d('forum', 'My Profile', true), array('controller' => 'users', 'action' => 'profile', $cupcake->user('id'), 'admin' => false));
            $links[] = $html->link(__d('forum', 'Edit Profile', true), array('controller' => 'users', 'action' => 'edit', 'admin' => false));
            $links[] = $html->link(__d('forum', 'Logout', true), array('controller' => 'users', 'action' => 'logout', 'admin' => false));
        } else {
            $links[] = $html->link(__d('forum', 'Login', true), array('controller' => 'users', 'action' => 'login'));
            $links[] = $html->link(__d('forum', 'Sign Up', true), array('controller' => 'users', 'action' => 'signup'));
            $links[] = $html->link(__d('forum', 'Forgot Password', true), array('controller' => 'users', 'action' => 'forgot'));
        }
        
        $links[] = $time->nice(time(), $cupcake->timezone());
        echo implode(' - ', $links); ?>
    </span>
    
    <?php echo $html->getCrumbs(' &raquo; '); ?>
    <span class="clear"><!-- --></span>
</div>
