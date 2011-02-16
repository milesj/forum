
<?php // Search orderbY
$orderBy = array(
	'LastPost.created' => __d('forum', 'Last post time', true),
	'Topic.created' => __d('forum', 'Topic created time', true),
	'Topic.post_count' => __d('forum', 'Total posts', true),
	'Topic.view_count' => __d('forum', 'Total views', true)
); ?>

<div class="forumHeader">
	<h2>Search</h2>
</div>

<?php echo $this->Form->create('Topic', array('url' => array('controller' => 'search', 'action' => 'proxy'))); ?>
<div id="search">
	<table cellpadding="5" style="width: 100%">
    <tr>
    	<td class="ar"><?php echo $this->Form->label('keywords', __d('forum', 'Keywords', true) .':'); ?></td>
    	<td><?php echo $this->Form->input('keywords', array('div' => false, 'label' => false, 'style' => 'width: 300px')); ?></td>

		<td class="ar"><?php echo $this->Form->input('power', array('div' => false, 'label' => false, 'type' => 'checkbox')); ?></td>
    	<td><?php echo $this->Form->label('power', __d('forum', 'Power Search?', true)); ?></td>

		<td class="ar"><?php echo $this->Form->label('category', __d('forum', 'Within Forum Category', true) .':'); ?></td>
    	<td><?php echo $this->Form->input('category', array('div' => false, 'label' => false, 'options' => $forums, 'escape' => false, 'empty' => true)); ?></td>

		<td class="ar"><?php echo $this->Form->label('orderBy', __d('forum', 'Order By', true) .':'); ?></td>
    	<td><?php echo $this->Form->input('orderBy', array('div' => false, 'label' => false, 'options' => $orderBy)); ?></td>

		<td class="ar"><?php echo $this->Form->label('byUser', __d('forum', 'By User (Username)', true) .':'); ?></td>
    	<td><?php echo $this->Form->input('byUser', array('div' => false, 'label' => false, 'style' => 'width: 150px')); ?></td>
   	</tr>
    </table>
</div>
<?php echo $this->Form->end(__d('forum', 'Search Topics', true)); ?>

<?php // Is searching
if ($searching === true) { ?>

<div class="forumWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    <tr>
        <th colspan="2"><?php echo $paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Forum', true), 'Topic.forum_category_id'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Author', true), 'User.username'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
    </tr>
    
    <?php if (empty($topics)) { ?>
    <tr>
    	<td colspan="8" class="empty"><?php __d('forum', 'No results were found, please refine your search criteria.'); ?></td>
   	</tr>
    <?php } else {
		$counter = 0;
		foreach ($topics as $topic) {
        	$pages = $this->Cupcake->topicPages($topic['Topic']); ?>
   
   	<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac" style="width: 35px"><?php echo $this->Cupcake->topicIcon($topic); ?></td>
        <td>
        	<?php if (!empty($topic['Poll']['id'])) { 
				echo $this->Html->image('/forum/img/poll.png', array('alt' => 'Poll', 'class' => 'img'));
			} ?>
            
        	<?php echo $this->Cupcake->topicType($topic['Topic']['type']); ?> 
        	<strong><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></strong>
            
            <?php if (count($pages) > 1) { ?>
            <br /><span class="gray"><?php __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</span>
            <?php } ?>
        </td>
        <td class="ac"><?php echo $this->Html->link($topic['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['slug'])); ?></td>
        <td class="ac"><?php echo $this->Html->link($topic['User']['username'], array('controller' => 'users', 'action' => 'profile', $topic['User']['id'])); ?></td>
        <td class="ac"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Cupcake->timezone()); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['post_count']); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['view_count']); ?></td>
        <td>
            <?php // Last activity
            if (!empty($topic['LastPost'])) {
                $lastTime = (!empty($topic['LastPost']['created'])) ? $topic['LastPost']['created'] : $topic['Topic']['modified']; ?>
                
                <em><?php echo $this->Time->relativeTime($lastTime, array('userOffset' => $this->Cupcake->timezone())); ?></em><br />
                <span class="gray"><?php __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['LastUser']['username'], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
                <?php echo $this->Html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], 'page' => $topic['Topic']['page_count'], '#' => 'post_'. $topic['Topic']['lastPost_id']))); ?>
            <?php } else {
				__d('forum', 'No latest activity to display');
			} ?>
        </td>
	</tr>
        
        <?php ++$counter; 
		} 
	} ?>
    </table>
    
	<?php echo $this->element('pagination'); ?>
</div>
<?php } ?>
