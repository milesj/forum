
<h2><?php __d('forum', 'Report User'); ?></h2>

<p><?php printf(__d('forum', 'Are you sure you want to report the user %s ? If so, please add a comment as to why you are reporting this user, and please be descriptive. Are they spamming, trolling, flaming, etc. 255 max characters.', true), $html->link($user['User']['username'], array('action' => 'profile', $user['User']['id']))); ?></p>

<?php $session->flash(); ?>

<?php echo $form->create('Report', array('url' => array('controller' => 'users', 'action' => 'report', $id))); ?>
<?php echo $form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
<?php echo $form->end(__d('forum', 'Report', true)); ?>