
<?php // User exists
if (!empty($user)) { ?>

<button type="button" onclick="goTo('<?php echo $html->url(array('action' => 'report', $user['User']['id'])); ?>');" class="fr button"><?php __d('forum', 'Report User'); ?></button>
<h2><?php echo $user['User']['username']; ?></h2>

<?php if (!empty($user['User']['signature'])) { ?>
<p><?php $decoda->parse($user['User']['signature'], false, array('b', 'i', 'u', 'img', 'url', 'align', 'color', 'size', 'code')); ?></p>
<?php } ?>

<table cellpadding="5" cellspacing="0" id="userInfo">
<tr>
	<td><strong><?php __d('forum', 'Joined'); ?>:</strong></td>
    <td><?php echo $time->nice($user['User']['created'], $cupcake->timezone()); ?></td>
	<td><strong><?php __d('forum', 'Total Topics'); ?>:</strong></td>
    <td><?php echo number_format($user['User']['totalTopics']); ?></td>
    <td><strong><?php __d('forum', 'Roles'); ?>:</strong></td>
    <td>
    	<?php if (!empty($user['Access'])) { 
			$roles = array();
			foreach ($user['Access'] as $access) {
				$roles[] = $access['AccessLevel']['title'];
			}
			echo implode(', ', $roles);
		} else {
			echo '<em>'. __d('forum', 'N/A', true) .'</em>';
		} ?>
    </td>
</tr>
<tr>
    <td><strong><?php __d('forum', 'Last Login'); ?>:</strong></td>
    <td>
		<?php if (!empty($user['User']['lastLogin'])) {
			echo $time->relativeTime($user['User']['lastLogin'], array('userOffset' => $cupcake->timezone()));
		} else {
			echo '<em>'. __d('forum', 'Never', true) .'</em>';
		} ?>
    </td>
    <td><strong><?php __d('forum', 'Total Posts'); ?>:</strong></td>
    <td><?php echo number_format($user['User']['totalPosts']); ?></td>
    <td><strong><?php __d('forum', 'Moderates'); ?>:</strong></td>
    <td>
    	<?php if (!empty($user['Moderator'])) { 
			$mods = array();
			foreach ($user['Moderator'] as $mod) {
				$mods[] = $html->link($mod['ForumCategory']['title'], array('controller' => 'category', 'action' => 'view', $mod['ForumCategory']['id']));
			}
			echo implode(', ', $mods);
		} else {
			echo '<em>'. __d('forum', 'N/A', true) .'</em>';
		} ?>
    </td>
</tr>
</table>

<?php // Topics
if (!empty($topics)) { ?>
<div class="forumWrap">
    <h3><?php __d('forum', 'Latest Topics'); ?></h3>
    
    <table class="table" cellspacing="0">
    <tr>
        <th><?php __d('forum', 'Topic'); ?></th>
        <th><?php __d('forum', 'Created'); ?></th>
        <th><?php __d('forum', 'Posts'); ?></th>
        <th><?php __d('forum', 'Views'); ?></th>
        <th><?php __d('forum', 'Last Activity'); ?></th>
    </tr>
    
    <?php $counter = 0;
    foreach ($topics as $topic) {
        $lastTime = (isset($topic['LastPost']['created'])) ? $topic['LastPost']['created'] : $topic['Topic']['modified']; ?>
        
    <tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
        <td><?php echo $html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?></td>
        <td class="ac"><?php echo $time->niceShort($topic['Topic']['created'], $cupcake->timezone()); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['post_count']); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['view_count']); ?></td>
        <td><?php echo $time->relativeTime($lastTime, array('userOffset' => $cupcake->timezone())); ?></td>
    </tr>
    <?php ++$counter; } ?>
    
    </table>
</div>    
<?php } ?>

<?php // Posts
if (!empty($posts)) { ?>
<div class="forumWrap">
    <h3><?php __d('forum', 'Latest Posts'); ?></h3>
    
    <table class="table" cellspacing="0">
    <tr>
        <th><?php __d('forum', 'Topic'); ?></th>
        <th><?php __d('forum', 'Author'); ?></th>
        <th><?php __d('forum', 'Posted On'); ?></th>
    </tr>
    
    <?php foreach($posts as $post) { ?>
    <tr class="altRow">
        <td><strong><?php echo $html->link($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['id'])); ?></strong></td>
        <td><?php echo $html->link($post['Topic']['User']['username'], array('controller' => 'users', 'action' => 'profile', $post['Topic']['User']['id'])); ?></td>
        <td class="ar"><?php echo $time->relativeTime($post['Post']['created'], array('userOffset' => $cupcake->timezone())); ?></td>
    </tr>
    <tr>
        <td colspan="3"><?php echo $decoda->parse($post['Post']['content']); ?></td>
    </tr>
    <?php } ?>
    
    </table>
</div>    
<?php } ?>

<?php } else { ?>

<h2><?php __d('forum', 'Not Found'); ?></h2>
<?php __d('forum', 'The user you are looking for does not exist.'); ?>

<?php } ?>