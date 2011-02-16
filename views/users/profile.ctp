
<?php // User exists
if (!empty($user)) { ?>

<div class="forumHeader">
	<button type="button" onclick="goTo('<?php echo $this->Html->url(array('action' => 'report', $user['User']['id'])); ?>');" class="fr button"><?php __d('forum', 'Report User'); ?></button>
	<h2><?php echo $user['User']['username']; ?></h2>
</div>

<?php if (!empty($user['User'][$this->Cupcake->columnMap['signature']])) { ?>
<p><?php $this->Decoda->parse($user['User'][$this->Cupcake->columnMap['signature']], false, array('b', 'i', 'u', 'img', 'url', 'align', 'color', 'size', 'code')); ?></p>
<?php } ?>

<table cellpadding="5" cellspacing="0" id="userInfo">
<tr>
	<td><strong><?php __d('forum', 'Joined'); ?>:</strong></td>
    <td><?php echo $this->Time->nice($user['User']['created'], $this->Cupcake->timezone()); ?></td>
	<td><strong><?php __d('forum', 'Total Topics'); ?>:</strong></td>
    <td><?php echo number_format($user['User'][$this->Cupcake->columnMap['totalTopics']]); ?></td>
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
		<?php if (!empty($user['User'][$this->Cupcake->columnMap['lastLogin']])) {
			echo $this->Time->relativeTime($user['User'][$this->Cupcake->columnMap['lastLogin']], array('userOffset' => $this->Cupcake->timezone()));
		} else {
			echo '<em>'. __d('forum', 'Never', true) .'</em>';
		} ?>
    </td>
    <td><strong><?php __d('forum', 'Total Posts'); ?>:</strong></td>
    <td><?php echo number_format($user['User'][$this->Cupcake->columnMap['totalPosts']]); ?></td>
    <td><strong><?php __d('forum', 'Moderates'); ?>:</strong></td>
    <td>
    	<?php if (!empty($user['Moderator'])) { 
			$mods = array();
			foreach ($user['Moderator'] as $mod) {
				$mods[] = $this->Html->link($mod['ForumCategory']['title'], array('controller' => 'category', 'action' => 'view', $mod['ForumCategory']['id']));
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
        <td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
        <td class="ac"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Cupcake->timezone()); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['post_count']); ?></td>
        <td class="ac"><?php echo number_format($topic['Topic']['view_count']); ?></td>
        <td><?php echo $this->Time->relativeTime($lastTime, array('userOffset' => $this->Cupcake->timezone())); ?></td>
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
        <td><strong><?php echo $this->Html->link($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug'])); ?></strong></td>
        <td><?php echo $this->Html->link($post['Topic']['User']['username'], array('controller' => 'users', 'action' => 'profile', $post['Topic']['User']['id'])); ?></td>
        <td class="ar"><?php echo $this->Time->relativeTime($post['Post']['created'], array('userOffset' => $this->Cupcake->timezone())); ?></td>
    </tr>
    <tr>
        <td colspan="3"><?php echo $this->Decoda->parse($post['Post']['content']); ?></td>
    </tr>
    <?php } ?>
    
    </table>
</div>    
<?php } ?>

<?php } else { ?>

<h2><?php __d('forum', 'Not Found'); ?></h2>
<?php __d('forum', 'The user you are looking for does not exist.'); ?>

<?php } ?>