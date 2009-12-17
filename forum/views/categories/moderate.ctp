
<?php // Crumbs
$html->addCrumb($category['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($category['Parent']['id'])) {
	$html->addCrumb($category['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $category['Parent']['id']));
}
$html->addCrumb($category['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $category['ForumCategory']['id'])); ?>

<h2><?php __d('forum', 'Moderate'); ?>: <?php echo $category['ForumCategory']['title']; ?></h2>

<?php $session->flash(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Return to Forum', true), array('controller' => 'categories', 'action' => 'view', $category['ForumCategory']['id'])); ?>
</div>

<?php echo $form->create('Topic', array('url' => array('controller' => 'categories', 'action' => 'moderate', $category['ForumCategory']['id']))); ?>
<div id="topicWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    <tr>
    	<th style="width: 25px" class="ac"><input type="checkbox" onclick="toggleCheckboxes(this, 'Topic', 'items');" /></th>
        <th><?php echo $paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Status', true), 'Topic.status'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Author', true), 'User.username'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
    </tr>
    
    <?php // Topics
	if (!empty($topics)) {
		$counter = 0;
		foreach ($topics as $topic) {
			$pages = $cupcake->topicPages($topic['Topic']); ?>
        
   	<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac"><input type="checkbox" name="data[Topic][items][]" value="<?php echo $topic['Topic']['id']; ?>" /></td>
        <td>
        	<?php echo $cupcake->topicType($topic['Topic']['status']); ?>
			<?php if (!empty($topic['Poll']['id'])) { 
				echo $html->image('/forum/img/poll.png', array('alt' => 'Poll'));
			} ?>
            
        	<strong><?php echo $html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?></strong>
            
            <?php if (count($pages) > 1) { ?>
            <br /><span class="gray"><?php __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</span>
            <?php } ?>
        </td>
        <td class="ac"><?php echo $cupcake->options(2, $topic['Topic']['status']); ?></td>
        <td class="ac"><?php echo $html->link($topic['User']['username'], array('controller' => 'users', 'action' => 'profile', $topic['User']['id'])); ?></td>
        <td class="ac"><?php echo $time->niceShort($topic['Topic']['created'], $cupcake->timezone()); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['post_count']); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['view_count']); ?></td>
        <td>
            <?php // Last activity
            if (!empty($topic['LastPost'])) {
                $lastTime = (!empty($topic['LastPost']['created'])) ? $topic['LastPost']['created'] : $topic['Topic']['modified']; ?>
                
                <em><?php echo $time->relativeTime($lastTime, array('userOffset' => $cupcake->timezone())); ?></em><br />
                <span class="gray"><?php __d('forum', 'by'); ?> <?php echo $html->link($topic['LastUser']['username'], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
                <?php echo $html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'], 'page' => $topic['Topic']['page_count'], '#' => 'post_'. $topic['Topic']['lastPost_id']))); ?>
            <?php } else {
				__d('forum', 'No latest activity to display');
			} ?>
        </td>
	</tr>
    	
		<?php ++$counter;
		}
	} else { ?>
    
    <tr>
    	<td colspan="7" class="empty"><?php __d('forum', 'There are no topics within this forum category.'); ?></td>
   	</tr>
    
    <?php } ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<?php echo $form->input('action', array('options' => array(
	'move' => __d('forum', 'Move Topic(s)', true),
	'open' => __d('forum', 'Open Topic(s)', true),
	'close' => __d('forum', 'Close Topic(s)', true),
	'delete' => __d('forum', 'Delete Topic(s)', true)),
	'div' => false, 
	'label' => __d('forum', 'Perform Action', true) .': '
)); ?>
<?php echo $form->input('move_id', array('options' => $forums, 'div' => false, 'label' => __d('forum', 'Move To', true) .': ', 'escape' => false)); ?>
<?php echo $form->submit(__d('forum', 'Process', true), array('div' => false)); ?>
<?php echo $form->end(); ?>

<div class="forumOptions">
	<?php echo $html->link(__d('forum', 'Return to Forum', true), array('controller' => 'categories', 'action' => 'view', $category['ForumCategory']['id'])); ?>
</div>
