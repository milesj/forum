
<h2><?php __d('forum', 'Report Post'); ?></h2>

<p><?php printf(__d('forum', 'Are you sure you want to report the post (below) in the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.', true), $html->link($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['id']))); ?></p>

<?php $session->flash(); ?>

<?php echo $form->create('Report', array('url' => array('controller' => 'posts', 'action' => 'report', $id))); ?>
<?php echo $form->input('post', array('type' => 'textarea', 'value' => $post['Post']['content'], 'readonly' => 'readonly', 'label' => __d('forum', 'Post', true))); ?>
<?php echo $form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
<?php echo $form->end(__d('forum', 'Report', true)); ?>