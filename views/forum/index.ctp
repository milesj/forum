<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($forums)) {
	foreach ($forums as $forum) { ?>

<div class="container" id="forum-<?php echo $forum['Forum']['id']; ?>">
	<div class="containerHeader">
		<a href="javascript:;" onclick="return Forum.toggleForums(this, <?php echo $forum['Forum']['id']; ?>);" class="toggle">-</a>
		<h3><?php echo $forum['Forum']['title']; ?></h3>
	</div>
    
	<div class="containerContent" id="forums-<?php echo $forum['Forum']['id']; ?>">
		<table cellspacing="0" class="table forums">
			<thead>
				<tr>
					<th colspan="2"><?php __d('forum', 'Forum'); ?></th>
					<th style="width: 10%"><?php __d('forum', 'Topics'); ?></th>
					<th style="width: 10%"><?php __d('forum', 'Posts'); ?></th>
					<th style="width: 30%"><?php __d('forum', 'Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if (!empty($forum['Children'])) {
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
</div>

<?php } } ?>

<div class="statistics">
	<?php if (!$this->Common->user()) { ?>
		<div class="fr">
			<?php echo $this->element('login'); ?>
		</div>
    <?php } ?>
    
	<div class="totalStats">
		<strong><?php __d('forum', 'Statistics'); ?></strong>: <?php printf(__d('forum', '%d topics, %d posts, and %d users', true), $totalTopics, $totalPosts, $totalUsers); ?>
	</div>
    
    <?php if (!empty($newestUser)) { ?>
		<div class="newestUser">
			<strong><?php __d('forum', 'Newest User'); ?></strong>: <?php echo $this->Html->link($newestUser['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $newestUser['User']['id'])); ?>
		</div>
   	<?php } ?>
     
    <?php if (!empty($whosOnline)) {
		$onlineUsers = array();
		
		foreach ($whosOnline as $online) {
			$onlineUsers[] = $this->Html->link($online['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $online['User']['id']));
		} ?>
        
		<div class="whosOnline">
			<strong><?php __d('forum', 'Whos Online'); ?></strong>: <?php echo implode(', ', $onlineUsers); ?>
		</div>
    <?php } ?>
</div>
