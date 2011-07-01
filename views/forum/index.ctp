
<?php // Crumbs
$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index')); ?>

<?php // Forums
if (!empty($forums)) {
	foreach ($forums as $forum) { ?>

<div class="forumWrap" id="forum-<?php echo $forum['Forum']['id']; ?>">
	<h3><a href="#toggle" onclick="toggleElement('forumSubs-<?php echo $forum['Forum']['id']; ?>', this); return false;" class="fr">-</a> <?php echo $forum['Forum']['title']; ?></h3>
    
    <table cellspacing="0" class="table" id="forumSubs-<?php echo $forum['Forum']['id']; ?>">
		<thead>
			<tr>
				<th colspan="2"><?php __d('forum', 'Forum'); ?></th>
				<th style="width: 10%"><?php __d('forum', 'Topics'); ?></th>
				<th style="width: 10%"><?php __d('forum', 'Posts'); ?></th>
				<th style="width: 30%"><?php __d('forum', 'Activity'); ?></th>
			</tr>
		</thead>
		<tbody>

		<?php // Categories
		if (!empty($forum['Children'])) {
			$counter = 0;

			foreach ($forum['Children'] as $child) {
				echo $this->element('tiles/forum_row', array(
					'forum' => $child,
					'counter' => $counter
				));

				++$counter;
			}
		} else { ?>

			<tr>
				<td colspan="5" class="empty"><?php __d('forum', 'There are no categories within this forum.'); ?></td>
			</tr>

		<?php } ?>

		</tbody>
    </table>
</div>
<?php } } ?>

<div id="forumStats">
	<?php if (!$this->Common->user()) { ?>
		<div class="fr">
			<?php echo $this->element('login'); ?>
		</div>
    <?php } ?>
    
	<strong><?php __d('forum', 'Statistics'); ?></strong>: <?php printf(__d('forum', '%d topics, %d posts, and %d users', true), $totalTopics, $totalPosts, $totalUsers); ?><br />
    
    <?php // Newest user
	if (!empty($newestUser)) { ?>
		<strong><?php __d('forum', 'Newest User'); ?></strong>: <?php echo $this->Html->link($newestUser['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $newestUser['User']['id'])); ?>
   	<?php } ?>
     
    <?php // Whos online
	if (!empty($whosOnline)) {
		$online = array();
		
		foreach ($whosOnline as $user) {
			$online[] = $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $user['User']['id']));
		} ?>
        
		<div id="whosOnline">
			<strong><?php __d('forum', 'Whos Online'); ?></strong>: <?php echo implode(', ', $online); ?>
		</div>
    <?php } ?>
</div>
