
<h2><?php __d('forum', 'Staff &amp; Moderators'); ?></h2>

<?php $session->flash(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Add Staff', true), array('action' => 'add_access')); ?>
	<?php echo $html->link(__d('forum', 'Add Access Level', true), array('action' => 'add_access_level')); ?>
    <?php echo $html->link(__d('forum', 'Add Moderator', true), array('action' => 'add_moderator')); ?>
</div>

<?php // Form
echo $form->create('Access', array('url' => array('controller' => 'staff', 'action' => 'index', 'admin' => true))); ?>

<div class="forumWrap">
	<h3><?php __d('forum', 'Levels'); ?></h3>
     
    <table cellspacing="0" class="table">
    <tr>
        <th style="width: 33%"><?php __d('forum', 'Title'); ?></th>
        <th style="width: 33%"><?php __d('forum', 'Level'); ?></th>
        <th style="width: 33%"><?php __d('forum', 'Options'); ?></th>
    </tr>
    
    <?php // Levels
	foreach ($levels as $level) { ?>
    
    <tr id="level_<?php echo $level['AccessLevel']['id']; ?>">
        <td class="ac"><?php echo $level['AccessLevel']['title']; ?></td>
        <td class="ac"><?php echo $level['AccessLevel']['level']; ?></td>
        <td class="ac gray">
        	<?php if ($level['AccessLevel']['id'] <= 4) { ?>
        		<em><?php __d('forum', 'Restricted'); ?></em>
        	<?php } else { ?>
        	<?php echo $html->link(__d('forum', 'Edit', true), array('action' => 'edit_access_level', $level['AccessLevel']['id'])); ?> -
        	<?php echo $html->link(__d('forum', 'Delete', true), array('action' => 'delete_access_level', $level['AccessLevel']['id']));
			} ?>
        </td>
    </tr>
    
    <?php } ?>
    
    </table>
</div>

<div class="forumWrap">
	<h3><?php __d('forum', 'Staff'); ?></h3>
     
    <table cellspacing="0" class="table">
    <tr>
        <th style="width: 25%"><?php __d('forum', 'User'); ?></th>
        <th style="width: 25%"><?php __d('forum', 'Access Level'); ?></th>
        <th style="width: 25%"><?php __d('forum', 'Achieved On'); ?></th>
        <th style="width: 25%"><?php __d('forum', 'Options'); ?></th>
    </tr>
    
    <?php // Staff
	foreach ($staff as $user) { ?>
    
    <tr id="staff_<?php echo $user['Access']['id']; ?>">
        <td><strong><?php echo $html->link($user['User']['username'], array('controller' => 'users', 'action' => 'edit', $user['User']['id'], 'admin' => true)); ?></strong></td>
        <td class="ac"><?php echo $user['AccessLevel']['title']; ?></td>
        <td class="ac"><?php echo $time->nice($user['Access']['created'], $cupcake->timezone()); ?></td>
        <td class="ac gray">
        	<?php echo $html->link(__d('forum', 'Edit', true), array('action' => 'edit_access', $user['Access']['id'])); ?> -
        	<?php echo $html->link(__d('forum', 'Delete', true), array('action' => 'delete_access', $user['Access']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true))); ?>
        </td>
    </tr>
    
    <?php } ?>
    
    </table>
</div>

<?php echo $form->end(); ?>

<?php // Form
echo $form->create('Moderator', array('url' => array('controller' => 'staff', 'action' => 'index', 'admin' => true))); ?>

<div class="forumWrap">
	<h3><?php __d('forum', 'Moderators'); ?></h3>
     
    <table cellspacing="0" class="table">
    <tr>
        <th style="width: 25%"><?php __d('forum', 'User'); ?></th>
        <th style="width: 25%"><?php __d('forum', 'Moderates'); ?></th>
        <th style="width: 25%"><?php __d('forum', 'Achieved On'); ?></th>
        <th style="width: 25%"><?php __d('forum', 'Options'); ?></th>
    </tr>
    
    <?php // Moderator
    if (!empty($mods)) {
		foreach ($mods as $user) { ?>
    
    <tr id="mod_<?php echo $user['Moderator']['id']; ?>">
        <td><strong><?php echo $html->link($user['User']['username'], array('controller' => 'users', 'action' => 'edit', $user['Moderator']['id'], 'admin' => true)); ?></strong></td>
        <td class="ac"><?php echo $html->link($user['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'edit_category', $user['ForumCategory']['id'], 'admin' => true)); ?></td>
        <td class="ac"><?php echo $time->nice($user['Moderator']['created'], $cupcake->timezone()); ?></td>
        <td class="ac gray">
        	<?php echo $html->link(__d('forum', 'Edit', true), array('action' => 'edit_moderator', $user['Moderator']['id'])); ?> -
        	<?php echo $html->link(__d('forum', 'Delete', true), array('action' => 'delete_moderator', $user['Moderator']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true))); ?>
        </td>
    </tr>
    
    	<?php }
    } else { ?>
    
    <tr>
    	<td colspan="4" class="empty"><?php __d('forum', 'There are no assigned moderators.'); ?> <?php echo $html->link(__d('forum', 'Add Moderator', true), array('action' => 'add_moderator')); ?>.</td>
    </tr>
    
    <?php } ?>
    
    </table>
</div>

<?php echo $form->end(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Add Staff', true), array('action' => 'add_access')); ?>
	<?php echo $html->link(__d('forum', 'Add Access Level', true), array('action' => 'add_access_level')); ?>
    <?php echo $html->link(__d('forum', 'Add Moderator', true), array('action' => 'add_moderator')); ?>
</div>

