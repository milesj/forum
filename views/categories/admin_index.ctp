
<h2><?php __d('forum', 'Manage Forums'); ?></h2>

<?php $session->flash(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Add Forum', true), array('action' => 'add_forum')); ?>
    <?php echo $html->link(__d('forum', 'Add Category', true), array('action' => 'add_category')); ?>
</div>

<?php // Form
echo $form->create('ForumCategory', array('url' => array('controller' => 'categories', 'action' => 'index', 'admin' => true)));

// Forums
if (!empty($forums)) {
	foreach ($forums as $forum) { ?>

<div class="forumWrap" id="forum_<?php echo $forum['Forum']['id']; ?>">
	<h3>
    	<span class="fr">
			<?php echo $html->link(__d('forum', 'Edit', true), array('action' => 'edit_forum', $forum['Forum']['id'])); ?> -
            <?php echo $html->link(__d('forum', 'Delete', true), array('action' => 'delete_forum', $forum['Forum']['id'])); ?>
        </span>
        
		<?php // Order
        echo $form->input('Forum.'. $forum['Forum']['id'] .'.orderNo', array('value' => $forum['Forum']['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 30px'));
        echo $form->input('Forum.'. $forum['Forum']['id'] .'.id', array('value' => $forum['Forum']['id'], 'type' => 'hidden')); ?>
        
		<?php echo $forum['Forum']['title']; ?> 
        <span class="gray">(<?php echo $cupcake->options(3, $forum['Forum']['status']); ?>)</span>
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
	if (!empty($forum['ForumCategory'])) {
		foreach ($forum['ForumCategory'] as $category) { ?>
    
    <tr id="category_<?php echo $category['id']; ?>">
    	<td>
			<?php // Order
			echo $form->input('ForumCategory.'. $category['id'] .'.orderNo', array('value' => $category['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 30px'));
			echo $form->input('ForumCategory.'. $category['id'] .'.id', array('value' => $category['id'], 'type' => 'hidden')); ?>
        </td>
        <td colspan="2"><strong><?php echo $html->link($category['title'], array('action' => 'edit_category', $category['id'])); ?></strong></td>
        <td class="ac"><?php echo $cupcake->options(2, $category['status']); ?></td>
        <td class="ac"><?php echo number_format($category['topic_count']); ?></td>
        <td class="ac"><?php echo number_format($category['post_count']); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $category['accessRead'], true); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $category['accessPost']); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $category['accessReply']); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $category['accessPoll']); ?></td>
        <td class="ac gray">
        	<?php echo $html->link(__d('forum', 'Edit', true), array('action' => 'edit_category', $category['id'])); ?> -
        	<?php echo $html->link(__d('forum', 'Delete', true), array('action' => 'delete_category', $category['id'])); ?>
        </td>
    </tr>

		<?php // Sub forums
		if (!empty($category['SubForum'])) {
			foreach ($category['SubForum'] as $child) { ?>
            
 	<tr id="category_<?php echo $child['id']; ?>" class="altRow">
    	<td>&nbsp;</td>
    	<td style="width: 35px">
			<?php // Order
			echo $form->input('ForumCategory.'. $child['id'] .'.orderNo', array('value' => $child['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 30px'));
			echo $form->input('ForumCategory.'. $child['id'] .'.id', array('value' => $child['id'], 'type' => 'hidden')); ?>
        </td>
        <td><strong><?php echo $html->link($child['title'], array('action' => 'edit_category', $child['id'])); ?></strong></td>
        <td class="ac"><?php echo $cupcake->options(2, $child['status']); ?></td>
        <td class="ac"><?php echo number_format($child['topic_count']); ?></td>
        <td class="ac"><?php echo number_format($child['post_count']); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $child['accessRead'], true); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $child['accessPost']); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $child['accessReply']); ?></td>
        <td class="ac"><?php echo $cupcake->options(4, $child['accessPoll']); ?></td>
        <td class="ac gray">
        	<?php echo $html->link(__d('forum', 'Edit', true), array('action' => 'edit_category', $child['id'])); ?> -
        	<?php echo $html->link(__d('forum', 'Delete', true), array('action' => 'delete_category', $child['id'])); ?>
        </td>
    </tr>
    
    	<?php }
		}
		} 
    } else { ?>
    
    <tr>
    	<td colspan="11" class="empty"><?php __d('forum', 'There are no categories within this forum.'); ?> <?php echo $html->link(__d('forum', 'Add Category', true), array('action' => 'add_category')); ?>.</td>
   	</tr>
    
    <?php } ?>
    
    </table>
</div>

<?php } }

echo $form->end(__d('forum', 'Update Order', true)); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Add Forum', true), array('action' => 'add_forum')); ?>
    <?php echo $html->link(__d('forum', 'Add Category', true), array('action' => 'add_category')); ?>
</div>

