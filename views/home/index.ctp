
<?php // Crumbs
$html->addCrumb($cupcake->settings['site_name'], array('controller' => 'home', 'action' => 'index')); ?>

<?php // Forums
if (!empty($forums)) {
	foreach ($forums as $forum) { ?>

<div class="forumWrap" id="forum_<?php echo $forum['Forum']['id']; ?>">
	<h3><a href="#toggle" onclick="toggleElement('forumCategories_<?php echo $forum['Forum']['id']; ?>', this); return false;" class="fr">-</a> <?php echo $forum['Forum']['title']; ?></h3>
    
    <table cellspacing="0" class="table" id="forumCategories_<?php echo $forum['Forum']['id']; ?>">
    <tr>
        <th colspan="2"><?php __d('forum', 'Forum'); ?></th>
        <th style="width: 10%"><?php __d('forum', 'Topics'); ?></th>
        <th style="width: 10%"><?php __d('forum', 'Posts'); ?></th>
        <th style="width: 30%"><?php __d('forum', 'Activity'); ?></th>
    </tr>
    
    <?php // Categories
	if (!empty($forum['ForumCategory'])) {
        $counter = 0;
		foreach ($forum['ForumCategory'] as $category) {
			$subForums = array();
			if (!empty($category['SubForum'])) {
				foreach ($category['SubForum'] as $sub) {
					$subForums[] = $html->link($sub['title'], array('controller' => 'categories', 'action' => 'view', $sub['id']));
				}
			} ?>
    
    <tr id="category_<?php echo $category['id']; ?>"<?php if ($counter % 2) echo ' class="altRow"'; ?>>
        <td class="ac" style="width: 35px"><?php echo $cupcake->forumIcon($category); ?></td>
        <td>
            <strong><?php echo $html->link($category['title'], array('controller' => 'categories', 'action' => 'view', $category['id'])); ?></strong><br />
            <?php echo $category['description']; ?>
            
            <?php if (!empty($subForums)) { ?>
            <div class="subForums">
                <span class="gray"><?php __d('forum', 'Sub-Forums'); ?>:</span> <?php echo implode(', ', $subForums); ?>
            </div>     
            <?php } ?>
        </td>
        <td class="ac"><?php echo number_format($category['topic_count']); ?></td>
        <td class="ac"><?php echo number_format($category['post_count']); ?></td>
        <td>
            <?php // Last activity
            if (!empty($category['LastTopic'])) {
                $lastTime = (!empty($category['LastPost']['created'])) ? $category['LastPost']['created'] : $category['LastTopic']['created']; ?>
                
                <?php echo $html->link($category['LastTopic']['title'], array('controller' => 'topics', 'action' => 'view', $category['lastTopic_id'])); ?>
                <?php echo $html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $category['lastTopic_id'], 'page' => $category['LastTopic']['page_count'], '#' => 'post_'. $category['lastPost_id']))); ?><br />
                
                <em><?php echo $time->relativeTime($lastTime, array('userOffset' => $cupcake->timezone())); ?></em> <span class="gray"><?php __d('forum', 'by'); ?> <?php echo $html->link($category['LastUser']['username'], array('controller' => 'users', 'action' => 'profile', $category['lastUser_id'])); ?></span>
            <?php } else {
				__d('forum', 'No latest activity to display');
            } ?>
        </td>
    </tr>

		<?php ++$counter;
		} 
    } else { ?>
    
    <tr>
    	<td colspan="5" class="empty"><?php __d('forum', 'There are no categories within this forum.'); ?></td>
   	</tr>
    
    <?php } ?>
    
    </table>
</div>
<?php } } ?>

<div id="forumStats">
	<?php if (!$cupcake->user()) { ?>
	<div class="fr">
    	<?php echo $this->element('login'); ?>
    </div>
    <?php } ?>
    
	<strong><?php __d('forum', 'Statistics'); ?></strong>: <?php printf(__d('forum', '%d topics, %d posts, and %d users', true), $totalTopics, $totalPosts, $totalUsers); ?><br />
    
    <?php // Newest user
	if (!empty($newestUser)) { ?>
    <strong><?php __d('forum', 'Newest User'); ?></strong>: <?php echo $html->link($newestUser['User']['username'], array('controller' => 'users', 'action' => 'profile', $newestUser['User']['id'])); ?>
   	<?php } ?>
     
    <?php // Whos online
	if (!empty($whosOnline)) {
		$online = array();
		foreach ($whosOnline as $user) {
			$online[] = $html->link($user['User']['username'], array('controller' => 'users', 'action' => 'profile', $user['User']['id']));
		} ?>
        
    <div id="whosOnline">
    	<strong><?php __d('forum', 'Whos Online'); ?></strong>: <?php echo implode(', ', $online); ?>
    </div>
    <?php } ?>
</div>
