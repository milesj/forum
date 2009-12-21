
<h2><?php __d('forum', 'Delete Access Level'); ?></h2>

<p><?php printf(__d('forum', 'Before you delete the level %s , please select which level a user should receive, if they have the level that will be deleted.', true), '<strong>'. $access['AccessLevel']['title'] .'</strong>'); ?></p>

<?php echo $form->create('AccessLevel', array('url' => array('controller' => 'staff', 'action' => 'delete_access_level', $id, 'admin' => true)));
echo $form->input('access_level_id', array('options' => $levels, 'escape' => false, 'label' => __d('forum', 'Move Users To', true)));
echo $form->end(__d('forum', 'Delete', true)); ?>