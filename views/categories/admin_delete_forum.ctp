
<h2><?php __d('forum', 'Delete Forum'); ?></h2>

<?php if (!empty($forums)) { ?>

<p><?php printf(__d('forum', 'Before you delete the forum %s, please select which forum all child categories should be moved to upon deletion.', true), '<strong>'. $forum['Forum']['title'] .'</strong>'); ?></p>

<?php echo $form->create('Forum', array('url' => array('controller' => 'categories', 'action' => 'delete_forum', $id, 'admin' => true)));
echo $form->input('forum_id', array('option' => $forums, 'label' => __d('forum', 'Forum', true)));
echo $form->end(__d('forum', 'Delete', true)); ?>

<?php } else { ?>

<p><?php __d('forum', 'You may not delete this forum, you must have at least one forum active.'); ?></p>

<?php } ?>