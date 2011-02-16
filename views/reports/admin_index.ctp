
<div class="forumHeader">
	<h2><?php __d('forum', 'Reported Items'); ?></h2>
</div>

<div class="forumOptions">
	<span><?php __d('forum', 'View Reported'); ?>:</span>
	<?php echo $this->Html->link(__d('forum', 'Topics', true), array('controller' => 'reports', 'action' => 'topics')); ?>
    <?php echo $this->Html->link(__d('forum', 'Posts', true), array('controller' => 'reports', 'action' => 'posts')); ?>
    <?php echo $this->Html->link(__d('forum', 'Users', true), array('controller' => 'reports', 'action' => 'users')); ?>
</div>

<?php echo $this->Form->create('Report', array('url' => array('controller' => 'reports', 'action' => 'index', 'admin' => true))); ?>
<div class="forumWrap">
    <?php echo $this->element('pagination'); ?>
    
    <table class="table" cellspacing="0">
    <tr>
        <th><?php __d('forum', 'Type'); ?></th>
        <th><?php __d('forum', 'Item'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Reported By', true), 'Reporter.username'); ?></th>
        <th><?php __d('forum', 'Comment'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Reported On', true), 'Report.created'); ?></th>
    </tr>
    
    <?php // List
	if (!empty($reports)) {
		$counter = 0;
		foreach ($reports as $report) { ?>
        
    <tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
        <td><?php echo $this->Html->link(__d('forum', ucfirst($report['Report']['itemType']), true), array('action' => $report['Report']['itemType'] .'s')); ?></td>
    	<td>	
        	<?php if ($report['Report']['itemType'] == 'topic') {
				echo $this->Html->link($report['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $report['Topic']['slug'], 'admin' => false));
			} else if ($report['Report']['itemType'] == 'user') {
				echo $this->Html->link($report['User']['username'], array('controller' => 'users', 'action' => 'edit', $report['User']['id'], 'admin' => true));
			} else if ($report['Report']['itemType'] == 'post') {
				echo $report['Post']['content'];
			} ?>
        </td>
        <td><?php echo $this->Html->link($report['Reporter']['username'], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['id'], 'admin' => true)); ?></td>
        <td><?php echo $report['Report']['comment']; ?></td>
        <td><?php echo $this->Time->nice($report['Report']['created'], $this->Cupcake->timezone()); ?></td>
    </tr>
    	<?php ++$counter; 
		}
	} else { ?>
    
    <tr>
    	<td colspan="5" class="empty"><?php __d('forum', 'There are no reported items to display.'); ?></td>
   	</tr>
    <?php } ?>
    
    </table>

	<?php echo $this->element('pagination'); ?>
</div>	

<?php echo $this->Form->end(); ?>