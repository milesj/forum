
<div class="forumHeader">
	<h2><?php __d('forum', 'Delete Access Level'); ?></h2>
</div>

<p><?php printf(__d('forum', 'Before you delete the level %s , please select which level a user should receive, if they have the level that will be deleted.', true), '<strong>'. $access['AccessLevel']['title'] .'</strong>'); ?></p>

<?php echo $this->Form->create('AccessLevel', array('url' => array('controller' => 'staff', 'action' => 'delete_access_level', $id, 'admin' => true)));
echo $this->Form->input('access_level_id', array('options' => $levels, 'escape' => false, 'label' => __d('forum', 'Move Users To', true)));
echo $this->Form->end(__d('forum', 'Delete', true)); ?>