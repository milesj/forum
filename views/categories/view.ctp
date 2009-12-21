
<?php // Crumbs
$html->addCrumb($category['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($category['Parent']['id'])) {
	$html->addCrumb($category['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $category['Parent']['id']));
}
$html->addCrumb($category['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $category['ForumCategory']['id'])); ?>

<?php if (!$cupcake->user()) { ?>
<div class="fr">
	<?php echo $this->element('login'); ?>
</div>
<?php } ?>
    
<h2><?php echo $category['ForumCategory']['title']; ?></h2>
<p><?php echo $category['ForumCategory']['description']; ?></p>

<?php if ($cupcake->user()) { ?>
<div class="forumOptions">
	<?php if ($cupcake->hasAccess('mod', $category['ForumCategory']['id'])) {
		echo $html->link(__d('forum', 'Moderate', true), array('controller' => 'categories', 'action' => 'moderate', $category['ForumCategory']['id']));
	} ?>
	<?php if ($category['ForumCategory']['status'] == 0) {
		if ($cupcake->hasAccess($category['ForumCategory']['accessPost'])) {
			echo $html->link(__d('forum', 'Create Topic', true), array('controller' => 'topics', 'action' => 'add', $category['ForumCategory']['id']));
		} ?>
		<?php if ($cupcake->hasAccess($category['ForumCategory']['accessPoll'])) {
			echo $html->link(__d('forum', 'Create Poll', true), array('controller' => 'topics', 'action' => 'add', $category['ForumCategory']['id'], 'poll'));
		}
	} else {
		echo '<span>'. __d('forum', 'Closed', true) .'</span>';
	} ?>
</div>
<?php } ?>

<?php // Sub Forums
if (!empty($category['SubForum'])) { ?>
<div class="forumWrap" id="subForums_<?php echo $category['ForumCategory']['id']; ?>">
	<h3><?php __d('forum', 'Sub-Forums'); ?></h3>
    
    <table cellspacing="0" class="table">
    <tr>
        <th colspan="2"><?php __d('forum', 'Forum'); ?></th>
        <th><?php __d('forum', 'Topics'); ?></th>
        <th><?php __d('forum', 'Posts'); ?></th>
        <th><?php __d('forum', 'Activity'); ?></th>
    </tr>
    
    <?php $counter = 0;
	foreach ($category['SubForum'] as $subCat) { ?>
    
    <tr id="category_<?php echo $subCat['id']; ?>"<?php if ($counter % 2) echo ' class="altRow"'; ?>>
        <td class="ac" style="width: 35px"><?php echo $cupcake->forumIcon($subCat); ?></td>
        <td>
            <strong><?php echo $html->link($subCat['title'], array('controller' => 'categories', 'action' => 'view', $subCat['id'])); ?></strong><br />
            <?php echo $subCat['description']; ?>
            
            <?php if (!empty($subForums)) { ?>
            <div class="subForums">
                <span class="gray"><?php __d('forum', 'Sub-Forums'); ?>:</span> <?php echo implode(', ', $subForums); ?>
            </div>     
            <?php } ?>
        </td>
        <td class="ac"><?php echo number_format($subCat['topic_count']); ?></td>
        <td class="ac"><?php echo number_format($subCat['post_count']); ?></td>
        <td>
            <?php // Last activity
            if (!empty($subCat['LastTopic'])) {
                $lastTime = (!empty($subCat['LastPost']['created'])) ? $subCat['LastPost']['created'] : $subCat['LastTopic']['created']; ?>
                
                <?php echo $html->link($subCat['LastTopic']['title'], array('controller' => 'topics', 'action' => 'view', $subCat['lastTopic_id'])); ?>
                <?php echo $html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $subCat['lastTopic_id'], 'page' => $subCat['LastTopic']['page_count'], '#' => 'post_'. $subCat['lastPost_id']))); ?><br />
                
                <em><?php echo $time->relativeTime($lastTime, array('userOffset' => $cupcake->timezone())); ?></em> <span class="gray"><?php __d('forum', 'by'); ?> <?php echo $html->link($subCat['LastUser']['username'], array('controller' => 'users', 'action' => 'profile', $subCat['lastUser_id'])); ?></span>
            <?php } else {
				__d('forum', 'No latest activity to display');
			} ?>
        </td>
    </tr>
    
	<?php ++$counter;
	} ?>
    
    </table>
</div>
<?php } ?>

<div id="topicWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    <tr>
        <th colspan="2"><?php echo $paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Author', true), 'User.username'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
        <th><?php echo $paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
    </tr>
    
    <?php // Stickies, etc
	$counter = 0;
	if (!empty($stickies)) { ?>

   	<tr class="altRow2">
    	<td colspan="7"><?php __d('forum', 'Important Topics'); ?></td>
 	</tr>
    
	<?php foreach ($stickies as $topic) {
        $pages = $cupcake->topicPages($topic['Topic']); ?>
        
   	<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac" style="width: 35px"><?php echo $cupcake->topicIcon($topic); ?></td>
        <td>
        	<?php if (!empty($topic['Poll']['id'])) { 
				echo $html->image('/forum/img/poll.png', array('alt' => 'Poll', 'class' => 'img'));
			} ?>
            
        	<?php echo $cupcake->topicType($topic['Topic']['type']); ?> 
        	<strong><?php echo $html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?></strong>
            
            <?php if (count($pages) > 1) { ?>
            <br /><span class="gray"><?php __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</span>
            <?php } ?>
        </td>
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
		} ?>
        
   	<tr class="altRow2">
    	<td colspan="7"><?php __d('forum', 'Regular Topics'); ?></td>
 	</tr>
    
	<?php }
    
    // Topics
	if (!empty($topics)) {
		foreach ($topics as $topic) {
			$pages = $cupcake->topicPages($topic['Topic']); ?>
        
   	<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td class="ac" style="width: 35px"><?php echo $cupcake->topicIcon($topic); ?></td>
        <td>
        	<?php if (!empty($topic['Poll']['id'])) { 
				echo $html->image('/forum/img/poll.png', array('alt' => 'Poll'));
			} ?>
            
        	<strong><?php echo $html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?></strong>
            
            <?php if (count($pages) > 1) { ?>
            <br /><span class="gray"><?php __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</span>
            <?php } ?>
        </td>
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

<div id="categoryStats">
	<?php if ($cupcake->user()) { ?>
    <div class="forumOptions fr">
		<?php if ($cupcake->hasAccess('mod', $category['ForumCategory']['id'])) {
            echo $html->link(__d('forum', 'Moderate', true), array('controller' => 'categories', 'action' => 'moderate', $category['ForumCategory']['id']));
        } ?>
        <?php if ($category['ForumCategory']['status'] == 0) {
            if ($cupcake->hasAccess($category['ForumCategory']['accessPost'])) {
                echo $html->link(__d('forum', 'Create Topic', true), array('controller' => 'topics', 'action' => 'add', $category['ForumCategory']['id']));
            } ?>
            <?php if ($cupcake->hasAccess($category['ForumCategory']['accessPoll'])) {
                echo $html->link(__d('forum', 'Create Poll', true), array('controller' => 'topics', 'action' => 'add', $category['ForumCategory']['id'], 'poll'));
            }
        } else {
            echo '<span>'. __d('forum', 'Closed', true) .'</span>';
        } ?>
    </div>
    <?php } ?>
    
    <?php // Moderators
	$moderators = array();
	if (!empty($category['Moderator'])) {
		foreach ($category['Moderator'] as $mod) {
			$moderators[] = $html->link($mod['User']['username'], array('controller' => 'users', 'action' => 'profile', $mod['User']['id'])); 
		}	
	} ?>

	<table cellspacing="5" style="width: 50%">
    <tr>
        <td class="ar"><?php __d('forum', 'Total Topics'); ?>: </td>
        <td><strong><?php echo $category['ForumCategory']['topic_count']; ?></strong></td>
        <td class="ar"><?php __d('forum', 'Increases Post Count'); ?>: </td>
        <td><strong><?php echo ($category['ForumCategory']['settingPostCount']) ? 'Yes' : 'No'; ?></strong></td>
        <td class="ar"><?php __d('forum', 'Can Read Topics'); ?>: </td>
        <td><strong><?php echo ($cupcake->hasAccess($category['ForumCategory']['accessRead'])) ? 'Yes' : 'No'; ?></strong></td>
        <td class="ar"><?php __d('forum', 'Can Create Topics'); ?>: </td>
        <td><strong><?php echo ($cupcake->hasAccess($category['ForumCategory']['accessPost'])) ? 'Yes' : 'No'; ?></strong></td>
    </tr>
    <tr>
        <td class="ar"><?php __d('forum', 'Total Posts'); ?>: </td>
        <td><strong><?php echo $category['ForumCategory']['post_count']; ?></strong></td>
        <td class="ar"><?php __d('forum', 'Auto-Lock Topics'); ?>: </td>
        <td><strong><?php echo ($category['ForumCategory']['settingAutoLock']) ? 'Yes' : 'No'; ?></strong></td>
        <td class="ar"><?php __d('forum', 'Can Reply'); ?>: </td>
        <td><strong><?php echo ($cupcake->hasAccess($category['ForumCategory']['accessReply'])) ? 'Yes' : 'No'; ?></strong></td>
        <td class="ar"><?php __d('forum', 'Can Create Polls'); ?>: </td>
        <td><strong><?php echo ($cupcake->hasAccess($category['ForumCategory']['accessPoll'])) ? 'Yes' : 'No'; ?></strong></td>
    </tr>
    <?php if (!empty($moderators)) { ?>
    <tr>
    	<td class="ar"><?php __d('forum', 'Moderators'); ?>: </td>
        <td colspan="7"><?php echo implode(', ', $moderators); ?></td>
   	</tr>
    <?php } ?>
    </table>
</div>
