<nav class="nav clear-after">
    <div class="nav-buttons">
        <?php
        if ($user) {
            echo $this->Html->link($user[$userFields['username']], $this->Admin->getUserRoute('profile', $user), array('class' => 'button'));
            echo $this->Html->link(__d('forum', 'View New Posts'), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false), array('class' => 'button'));
            echo $this->Html->link(__d('forum', 'Logout'), $userRoutes['logout'], array('class' => 'button error'));

        } else {
            echo $this->Html->link(__d('forum', 'Login'), $userRoutes['login'], array('class' => 'button'));

            if (!empty($userRoutes['signup'])) {
                echo $this->Html->link(__d('forum', 'Sign Up'), $userRoutes['signup'], array('class' => 'button'));
            }

            if (!empty($userRoutes['forgotPass'])) {
                echo $this->Html->link(__d('forum', 'Forgot Password'), $userRoutes['forgotPass'], array('class' => 'button'));
            }
        } ?>
    </div>

    <?php echo $this->Html->link(__d('forum', 'Forum'), array(
        'controller' => 'forum',
        'action' => 'index'
    ), array('class' => 'nav-brand')); ?>

    <ul class="nav-menu">
        <li<?php if ($menuTab === 'forums') echo ' class="is-active"'; ?>><?php echo $this->Html->link(__d('forum', 'Forums'), array('controller' => 'forum', 'action' => 'index')); ?></li>
        <li<?php if ($menuTab === 'search') echo ' class="is-active"'; ?>><?php echo $this->Html->link(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?></li>
        <li<?php if ($menuTab === 'rules') echo ' class="is-active"'; ?>><?php echo $this->Html->link(__d('forum', 'Rules'), array('controller' => 'forum', 'action' => 'rules')); ?></li>
        <li<?php if ($menuTab === 'help') echo ' class="is-active"'; ?>><?php echo $this->Html->link(__d('forum', 'Help'), array('controller' => 'forum', 'action' => 'help')); ?></li>

        <?php if ($user && $this->Admin->isAdmin()) { ?>
            <li><?php echo $this->Html->link(__d('forum', 'Admin'), array('controller' => 'admin', 'action' => 'index', 'plugin' => 'admin', 'admin' => false)); ?></li>
        <?php } ?>
    </ul>
</nav>
