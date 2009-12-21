
<h2><?php __d('forum', 'Report Topic'); ?></h2>

<p><?php printf(__d('forum', 'Are you sure you want to report the topic %s ? If so, please add a comment as to why you are reporting it, 255 max characters.', true), $html->link($topic['Topic']['title'], array('action' => 'view', $topic['Topic']['id']))); ?></p>

<?php $session->flash(); ?>

<?php echo $form->create('Report', array('url' => array('controller' => 'topics', 'action' => 'report', $id))); ?>
<?php echo $form->input('comment', array('type' => 'textarea', 'label' => __d('forum', 'Comment', true))); ?>
<?php echo $form->end(__d('forum', 'Report', true)); ?>