
<div class="forumHeader">
	<h2><?php __d('forum', 'Administration Panel'); ?></h2>
</div>

<div class="forumWrap">
    <table class="table" cellspacing="0">
    <tr>
        <th><?php __d('forum', 'Total Topics'); ?></th>
        <th><?php __d('forum', 'Total Posts'); ?></th>
        <th><?php __d('forum', 'Total Polls'); ?></th>
        <th><?php __d('forum', 'Total Users'); ?></th>
        <th><?php __d('forum', 'Total Reports'); ?></th>
        <th><?php __d('forum', 'Total Moderators'); ?></th>
        <th><?php __d('forum', 'Newest User'); ?></th>
    </tr>
    
    <tr>
    	<td class="ac"><?php echo number_format($totalTopics); ?></td>
    	<td class="ac"><?php echo number_format($totalPosts); ?></td>
    	<td class="ac"><?php echo number_format($totalPolls); ?></td>
    	<td class="ac"><?php echo number_format($totalUsers); ?></td>
    	<td class="ac"><?php echo number_format($totalReports); ?></td>
    	<td class="ac"><?php echo number_format($totalMods); ?></td>
    	<td class="ac"><?php echo $this->Html->link($newestUser['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $newestUser['User']['id'], 'admin' => true)); ?></td>
    </tr>
    </table>
</div>	

<?php if (!empty($latestReports)) { ?>

	<div class="forumWrap">
		<h3><?php __d('forum', 'Latest Reports'); ?></h3>

		<table class="table" cellspacing="0">
		<tr>
			<th><?php __d('forum', 'Type'); ?></th>
			<th><?php __d('forum', 'Item'); ?></th>
			<th><?php __d('forum', 'Reported By'); ?></th>
			<th><?php __d('forum', 'Comment'); ?></th>
			<th><?php __d('forum', 'Reported On'); ?></th>
		</tr>

		<?php // List
		$counter = 0;
		foreach ($latestReports as $report) { ?>

		<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
			<td><?php echo $this->Html->link(__d('forum', ucfirst($report['Report']['itemType']), true), array('controller' => 'reports', 'action' => $report['Report']['itemType'] .'s')); ?></td>
			<td>	
				<?php if ($report['Report']['itemType'] == Report::TOPIC) {
					echo $this->Html->link($report['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $report['Topic']['id'], 'admin' => false));
				
				} else if ($report['Report']['itemType'] == Report::USER) {
					echo $this->Html->link($report['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['User']['id'], 'admin' => true));
				
				} else if ($report['Report']['itemType'] == Report::POST) {
					echo $report['Post']['content'];
				} ?>
			</td>
			<td><?php echo $this->Html->link($report['Reporter'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['id'], 'admin' => true)); ?></td>
			<td><?php echo $report['Report']['comment']; ?></td>
			<td><?php echo $this->Time->nice($report['Report']['created'], $this->Common->timezone()); ?></td>
		</tr>

		<?php ++$counter; } ?>
		</table>
	</div>	

<?php }

if (!empty($latestUsers)) { ?>

	<div class="forumWrap">
		<h3><?php __d('forum', 'Latest Signed Up Users'); ?></h3>

		<table class="table" cellspacing="0">
		<tr>
			<th><?php __d('forum', 'Username'); ?></th>
			<th><?php __d('forum', 'Email'); ?></th>
			<th><?php __d('forum', 'Joined'); ?></th>
			<th><?php __d('forum', 'Topics'); ?></th>
			<th><?php __d('forum', 'Posts'); ?></th>
			<th><?php __d('forum', 'Options'); ?></th>
		</tr>

		<?php // List
		$counter = 0;
		foreach ($latestUsers as $user) { ?>

		<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
			<td><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['User']['id'], 'admin' => true)); ?></td>
			<td><?php echo $user['User']['email']; ?></td>
			<td class="ac"><?php echo $this->Time->nice($user['Profile']['created'], $this->Common->timezone()); ?></td>
			<td class="ac"><?php echo number_format($user['Profile']['totalTopics']); ?></td>
			<td class="ac"><?php echo number_format($user['Profile']['totalPosts']); ?></td>
			<td class="ac gray">
				<?php echo $this->Html->link(__d('forum', 'Edit', true), array('controller' => 'users', 'action' => 'edit', $user['User']['id'], 'admin' => true)); ?>
				<?php /*echo $this->Html->link(__d('forum', 'Delete', true), array('controller' => 'users', 'action' => 'delete', $user['User']['id'], 'admin' => true));*/ ?>
			</td>
		</tr>

		<?php ++$counter; } ?>
		</table>
	</div>	

<?php } ?>
