
<h2><?php __d('forum', 'Reported Topics'); ?></h2>

<div class="forumOptions">
	<span><?php __d('forum', 'View Reported'); ?>:</span>
	<?php echo $html->link(__d('forum', 'Topics', true), array('controller' => 'reports', 'action' => 'topics')); ?>
    <?php echo $html->link(__d('forum', 'Posts', true), array('controller' => 'reports', 'action' => 'posts')); ?>
    <?php echo $html->link(__d('forum', 'Users', true), array('controller' => 'reports', 'action' => 'users')); ?>
</div>

<?php echo $form->create('Report', array('url' => array('controller' => 'reports', 'action' => 'topics', 'admin' => true))); ?>
<div class="forumWrap">
    <?php echo $this->element('pagination'); ?>
    
    <table class="table" cellspacing="0">
    <tr>
    	<th style="width: 25px">&nbsp;</th>
        <th><?php echo $paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Reported By', true), 'Reporter.username'); ?></th>
        <th><?php __d('forum', 'Comment'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Reported On', true), 'Report.created'); ?></th>
    </tr>
    
    <?php // List
	if (!empty($reports)) {
		$counter = 0;
		foreach ($reports as $report) { ?>
        
    <tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac"><input type="checkbox" name="data[Report][items][]" value="<?php echo $report['Report']['id']; ?>:<?php echo $report['Topic']['id']; ?>" /></td>
        <td><?php echo $html->link($report['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $report['Topic']['id'], 'admin' => false)); ?></td>
        <td><?php echo $html->link($report['Reporter']['username'], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['id'], 'admin' => true)); ?></td>
        <td><?php echo $report['Report']['comment']; ?></td>
        <td><?php echo $time->nice($report['Report']['created'], $cupcake->timezone()); ?></td>
    </tr>
    	<?php ++$counter; 
		}
	} else { ?>
    
    <tr>
    	<td colspan="5" class="empty"><?php __d('forum', 'There are no reported topics.'); ?></td>
   	</tr>
    <?php } ?>
    
    </table>

	<?php echo $this->element('pagination'); ?>
</div>	

<?php echo $form->input('action', array('options' => array(
	'delete' => __d('forum', 'Delete Topic(s)', true),
	'close' => __d('forum', 'Close Topic(s)', true),
	'remove' => __d('forum', 'Remove Report Only', true)),
	'div' => false,
	'label' => __d('forum', 'Perform Action', true) .': '
)); ?>
<?php echo $form->submit(__d('forum', 'Process', true), array('div' => false)); ?>
<?php echo $form->end(); ?>