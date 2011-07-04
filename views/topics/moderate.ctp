
<?php // Crumbs
if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug'])); ?>

<div class="forumHeader">
	<h2><?php __d('forum', 'Moderate'); ?>: <?php echo $topic['Topic']['title']; ?></h2>
</div>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>
</div>

<?php echo $this->Form->create('Post', array('url' => $this->here)); ?>

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
        <td><?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></td>
        <td><?php echo $this->Text->truncate($post['Post']['content'], 100); ?></td>
		<td class="ac"><?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?></td>
    </tr>
    
    <?php ++$counter; 
	} ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>
</div>

<div class="moderateOptions">
	<?php 
	echo $this->Form->input('action', array('options' => array('delete' => __d('forum', 'Delete Post(s)', true)), 'div' => false, 'label' => __d('forum', 'Perform Action', true) .': '));
	echo $this->Form->submit(__d('forum', 'Process', true), array('div' => false)); ?>
</div>

<?php echo $this->Form->end(); ?>
