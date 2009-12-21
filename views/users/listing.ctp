
<h2><?php __d('forum', 'User List'); ?></h2>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'listing'))); ?>
<table cellpadding="5" style="width: 100%">
<tr>
	<td class="ar"><?php echo $form->input('username', array('div' => false, 'label' => __d('forum', 'Search Users (Username)', true) .': ')); ?></td>
	<td style="width: 75px"><?php echo $form->submit(__d('forum', 'Search', true), array('div' => false)); ?></td>
</tr>
</table>
<?php echo $form->end(); ?>

<div class="forumWrap">
    <?php echo $this->element('pagination'); ?>
    
    <table class="table" cellspacing="0">
    <tr>
        <th><?php echo $paginator->sort(__d('forum', 'Username', true), 'User.username'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Joined', true), 'User.created'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Last Active', true), 'User.lastLogin'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Topics', true), 'User.totalTopics'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Posts', true), 'User.totalPosts'); ?></th>
    </tr>
    
    <?php if (!empty($users)) {
		$counter = 0;
		foreach ($users as $user) { ?>
        
    <tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
        <td><?php echo $html->link($user['User']['username'], array('action' => 'profile', $user['User']['id'])); ?></td>
        <td class="ac"><?php echo $time->nice($user['User']['created'], $cupcake->timezone()); ?></td>
        <td class="ac">
            <?php if (!empty($user['User']['lastLogin'])) {
                echo $time->relativeTime($user['User']['lastLogin'], array('userOffset' => $cupcake->timezone()));
            } else {
                echo '<em>'. __d('forum', 'Never', true) .'</em>';
            } ?>
        </td>
        <td class="ac"><?php echo number_format($user['User']['totalTopics']); ?></td>
        <td class="ac"><?php echo number_format($user['User']['totalPosts']); ?></td>
    </tr>
    	<?php ++$counter; 
		}
	} else { ?>
    
    <tr>
    	<td colspan="5" class="empty"><?php __d('forum', 'There are no users signed up on this forum.'); ?></td>
   	</tr>
    <?php } ?>
    
    </table>

	<?php echo $this->element('pagination'); ?>
</div>	