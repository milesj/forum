
<?php // Crumbs
$html->addCrumb($topic['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($topic['ForumCategory']['Parent']['id'])) {
	$html->addCrumb($topic['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['Parent']['id']));
}
$html->addCrumb($topic['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['id'])); ?>

<h2><?php __d('forum', 'Moderate'); ?>: <?php echo $topic['Topic']['title']; ?></h2>

<?php $session->flash(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?>
</div>

<?php echo $form->create('Post', array('url' => array('controller' => 'topics', 'action' => 'moderate', $topic['Topic']['id']))); ?>
<div id="postWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
  	<tr>
    	<th style="width: 25px" class="ac"><input type="checkbox" onclick="toggleCheckboxes(this, 'Post', 'items');" /></th>
        <th><?php __d('forum', 'User'); ?></th>
        <th><?php __d('forum', 'Post'); ?></th>
        <th><?php __d('forum', 'Date'); ?></th>
   	</tr>
      
    <?php $counter = 0;
	foreach ($posts as $post) { ?>
    
    <tr id="post_<?php echo $post['Post']['id']; ?>"<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac">
        	<?php if ($post['Post']['id'] == $topic['Topic']['firstPost_id']) { ?>
        	<em class="gray">X</em>
            <?php } else { ?>
        	<input type="checkbox" name="data[Post][items][]" value="<?php echo $post['Post']['id']; ?>" />
            <?php } ?>
        </td>
        <td><?php echo $html->link($post['User']['username'], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></td>
        <td><?php echo $text->truncate($post['Post']['content'], 100); ?></td>
		<td class="ac"><?php echo $time->niceShort($post['Post']['created'], $cupcake->timezone()); ?></td>
    </tr>
    
    <?php ++$counter; 
	} ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<?php echo $form->input('action', array('options' => array('delete' => __d('forum', 'Delete Post(s)', true)), 'div' => false, 'label' => __d('forum', 'Perform Action', true) .': ')); ?>
<?php echo $form->submit(__d('forum', 'Process', true), array('div' => false)); ?>
<?php echo $form->end(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?>
</div>
