
<div class="forumHeader">
	<h2><?php __d('forum', 'Manage Users'); ?></h2>
</div>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'index', 'admin' => true))); ?>
<table cellpadding="5" style="width: 100%">
<tr>
	<td class="ar">
    	<?php __d('forum', 'Search Users'); ?>
		<?php echo $this->Form->input('username', array('div' => false, 'label' => '('. __d('forum', 'Username', true) .'): ')); ?>
        <?php echo $this->Form->input('id', array('div' => false, 'label' => '('. __d('forum', 'ID', true) .'): ', 'type' => 'text')); ?>
    </td>
	<td style="width: 75px"><?php echo $this->Form->submit(__d('forum', 'Search', true), array('div' => false)); ?></td>
</tr>
</table>
<?php echo $this->Form->end(); ?>

<div class="forumWrap">
    <?php echo $this->element('pagination'); ?>
    
    <table class="table" cellspacing="0">
    <tr>
    	<th>#</th>
        <th><?php echo $paginator->sort(__d('forum', 'Username', true), 'User.username'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Email', true), 'User.email'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Joined', true), 'User.created'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Last Active', true), 'User.'. $this->Cupcake->columnMap['lastLogin']); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Topics', true), 'User.'. $this->Cupcake->columnMap['totalTopics']); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Posts', true), 'User.'. $this->Cupcake->columnMap['totalPosts']); ?></th>
        <th><?php __d('forum', 'Options'); ?></th>
    </tr>
    
    <?php // List
	if (!empty($users)) {
		$counter = 0;
		foreach ($users as $user) { ?>
        
    <tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac"><?php echo $user['User']['id']; ?></td>
        <td><?php echo $this->Html->link($user['User']['username'], array('action' => 'edit', $user['User']['id'], 'admin' => true)); ?></td>
        <td><?php echo $user['User']['email']; ?></td>
        <td class="ac"><?php echo $this->Time->nice($user['User']['created'], $this->Cupcake->timezone()); ?></td>
        <td class="ac">
            <?php if (!empty($user['User']['lastLogin'])) {
                echo $this->Time->relativeTime($user['User'][$this->Cupcake->columnMap['lastLogin']], array('userOffset' => $this->Cupcake->timezone()));
            } else {
                echo '<em>'. __d('forum', 'Never', true) .'</em>';
            } ?>
        </td>
        <td class="ac"><?php echo number_format($user['User']['totalTopics']); ?></td>
        <td class="ac"><?php echo number_format($user['User']['totalPosts']); ?></td>
        <td class="ac gray">
        	<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit', $user['User']['id'], 'admin' => true)); ?> -
        	<?php echo $this->Html->link(__d('forum', 'Reset Password', true), array('action' => 'reset', $user['User']['id'], 'admin' => true), array('confirm' => __d('forum', 'Are you sure you want to reset?', true))); ?> -
        	<?php echo $this->Html->link(__d('forum', 'Delete', true), array('action' => 'delete', $user['User']['id'], 'admin' => true)); ?>
        </td>
    </tr>
    	<?php ++$counter; 
		}
	} else { ?>
    
    <tr>
    	<td colspan="5" class="empty"><?php __d('forum', 'There are no users signed up on this forum'); ?></td>
   	</tr>
    <?php } ?>
    
    </table>

	<?php echo $this->element('pagination'); ?>
</div>	