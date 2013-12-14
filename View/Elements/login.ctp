<?php if (!$user) { ?>

    <div class="login">
        <?php
        echo $this->Form->create('User', array('url' => $userRoutes['login'], 'class' => 'form--inline'));
        echo $this->Form->input($userFields['username'], array('error' => false, 'div' => 'field', 'label' => __d('forum', 'Username') . ': '));
        echo $this->Form->input($userFields['password'], array('error' => false, 'div' => 'field', 'label' => __d('forum', 'Password') . ': '));
        echo $this->Form->input('auto_login', array('type' => 'checkbox', 'error' => false, 'div' => 'field', 'label' => false, 'after' => ' ' . __d('forum', 'Remember Me?')));
        echo $this->Form->submit(__d('forum', 'Login'), array('class' => 'button small', 'div' => false));
        echo $this->Form->end(); ?>
    </div>

<?php } ?>