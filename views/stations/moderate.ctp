
<?php // Crumbs
$this->Html->addCrumb($settings['site_name'], array('controller' => 'home', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="forumHeader">
	<h2><?php __d('forum', 'Moderate'); ?>: <?php echo $forum['Forum']['title']; ?></h2>
</div>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Return to Forum', true), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>
</div>

<?php echo $this->Form->create('Topic', array('url' => $this->here)); ?>

<div id="topicWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    <tr>
    	<th style="width: 25px" class="ac"><input type="checkbox" onclick="toggleCheckboxes(this, 'Topic', 'items');" /></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Status', true), 'Topic.status'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Author', true), 'User.username'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
    </tr>
    
    <?php // Topics
	if (!empty($topics)) {
		$counter = 0;
		
		foreach ($topics as $topic) {
			echo $this->element('tiles/topic_row', array(
				'topic' => $topic,
				'counter' => $counter,
				'moderate' => true
			));
			
			++$counter;
		}
	} else { ?>
    
    <tr>
    	<td colspan="7" class="empty"><?php __d('forum', 'There are no topics within this forum category.'); ?></td>
   	</tr>
    
    <?php } ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<div class="moderateOptions">
	<?php echo $this->Form->input('action', array(
		'options' => array(
			'move' => __d('forum', 'Move Topic(s)', true),
			'open' => __d('forum', 'Open Topic(s)', true),
			'close' => __d('forum', 'Close Topic(s)', true),
			'delete' => __d('forum', 'Delete Topic(s)', true)
		),
		'div' => false, 
		'label' => __d('forum', 'Perform Action', true) .': '
	)); ?>
	
	<?php echo $this->Form->input('move_id', array('options' => $forums, 'div' => false, 'label' => __d('forum', 'Move To', true) .': ', 'escape' => false)); ?>
	<?php echo $this->Form->submit(__d('forum', 'Process', true), array('div' => false)); ?>
</div>

<?php echo $this->Form->end(); ?>

<div class="forumOptions">
	<?php echo $this->Html->link(__d('forum', 'Return to Forum', true), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>
</div>
