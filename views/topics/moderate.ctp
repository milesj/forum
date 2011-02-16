
<?php // Crumbs
$this->Html->addCrumb($topic['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($topic['ForumCategory']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['Parent']['slug']));
}
$this->Html->addCrumb($topic['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['slug'])); ?>

<div class="forumHeader">
	<h2><?php __d('forum', 'Moderate'); ?>: <?php echo $topic['Topic']['title']; ?></h2>
</div>

<?php echo $this->Session->flash(); ?>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>
</div>

<?php echo $this->Form->create('Post', array('url' => array('controller' => 'topics', 'action' => 'moderate', $topic['Topic']['slug']))); ?>
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
        <td><?php echo $this->Html->link($post['User']['username'], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></td>
        <td><?php echo $this->Text->truncate($post['Post']['content'], 100); ?></td>
		<td class="ac"><?php echo $this->Time->niceShort($post['Post']['created'], $this->Cupcake->timezone()); ?></td>
    </tr>
    
    <?php ++$counter; 
	} ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<?php echo $this->Form->input('action', array('options' => array('delete' => __d('forum', 'Delete Post(s)', true)), 'div' => false, 'label' => __d('forum', 'Perform Action', true) .': ')); ?>
<?php echo $this->Form->submit(__d('forum', 'Process', true), array('div' => false)); ?>
<?php echo $this->Form->end(); ?>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>
</div>
