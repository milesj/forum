
<div class="forumHeader">
	<h2><?php __d('forum', 'Manage Forums'); ?></h2>
</div>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Add Forum', true), array('action' => 'add')); ?>
</div>

<?php // Form
echo $this->Form->create('Forum', array('url' => $this->here));

// Forums
if (!empty($forums)) {
	foreach ($forums as $forum) { ?>

<div class="forumWrap" id="forum_<?php echo $forum['Forum']['id']; ?>">
	<h3>
    	<span class="fr">
			<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit', $forum['Forum']['id'])); ?> -
            <?php echo $this->Html->link(__d('forum', 'Delete', true), array('action' => 'delete', $forum['Forum']['id'])); ?>
        </span>
        
		<?php // Order
        echo $this->Form->input('Forum.'. $forum['Forum']['id'] .'.orderNo', array('value' => $forum['Forum']['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 30px'));
        echo $this->Form->input('Forum.'. $forum['Forum']['id'] .'.id', array('value' => $forum['Forum']['id'], 'type' => 'hidden')); ?>
        
		<?php echo $forum['Forum']['title']; ?> 
        <span class="gray">(<?php echo $this->Common->options(3, $forum['Forum']['status']); ?>)</span>
    </h3>
     
    <table cellspacing="0" class="table">
    <tr>
    	<th style="width: 35px">#</th>
        <th colspan="2"><?php __d('forum', 'Forum'); ?></th>
        <th><?php __d('forum', 'Status'); ?></th>
        <th><?php __d('forum', 'Topics'); ?></th>
        <th><?php __d('forum', 'Posts'); ?></th>
        <th><?php __d('forum', 'Read'); ?></th>
        <th><?php __d('forum', 'Post'); ?></th>
        <th><?php __d('forum', 'Reply'); ?></th>
        <th><?php __d('forum', 'Poll'); ?></th>
        <th><?php __d('forum', 'Options'); ?></th>
    </tr>
    
    <?php // Categories
	if (!empty($forum['SubForum'])) {
		foreach ($forum['SubForum'] as $subForum) {
			echo $this->element('admin/forum_row', array(
				'forum' => $subForum
			));

			if (!empty($subForum['Children'])) {
				foreach ($subForum['Children'] as $child) {
					echo $this->element('admin/forum_row', array(
						'forum' => $child,
						'child' => true
					));
				}
			}
		} 
    } else { ?>
    
		<tr>
			<td colspan="11" class="empty"><?php __d('forum', 'There are no categories within this forum.'); ?> <?php echo $this->Html->link(__d('forum', 'Add Category', true), array('action' => 'add_category')); ?>.</td>
		</tr>
    
    <?php } ?>
    
    </table>
</div>

<?php } }

echo $this->Form->end(__d('forum', 'Update Order', true)); ?>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Add Forum', true), array('action' => 'add')); ?>
</div>

