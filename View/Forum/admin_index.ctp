<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index')); ?>

<div class="container">
	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Total Topics'); ?></th>
					<th><?php echo __d('forum', 'Total Posts'); ?></th>
					<th><?php echo __d('forum', 'Total Polls'); ?></th>
					<th><?php echo __d('forum', 'Total Users'); ?></th>
					<th><?php echo __d('forum', 'Total Profiles'); ?></th>
					<th><?php echo __d('forum', 'Total Reports'); ?></th>
					<th><?php echo __d('forum', 'Total Moderators'); ?></th>
					<th><?php echo __d('forum', 'Newest User'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="align-center"><?php echo number_format($totalTopics); ?></td>
					<td class="align-center"><?php echo number_format($totalPosts); ?></td>
					<td class="align-center"><?php echo number_format($totalPolls); ?></td>
					<td class="align-center"><?php echo number_format($totalUsers); ?></td>
					<td class="align-center"><?php echo number_format($totalProfiles); ?></td>
					<td class="align-center"><?php echo number_format($totalReports); ?></td>
					<td class="align-center"><?php echo number_format($totalMods); ?></td>
					<td class="align-center"><?php echo $this->Html->link($newestUser['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $newestUser['Profile']['id'], 'admin' => true)); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php if ($latestReports) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Latest Reports'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Type'); ?></th>
					<th><?php echo __d('forum', 'Item'); ?></th>
					<th><?php echo __d('forum', 'Reported By'); ?></th>
					<th><?php echo __d('forum', 'Comment'); ?></th>
					<th><?php echo __d('forum', 'Reported On'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($latestReports as $counter => $report) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($this->Common->reportType($report['Report']['itemType']), array('controller' => 'reports', $report['Report']['itemType'])); ?></td>
					<td>
						<?php if ($report['Report']['itemType'] == Report::TOPIC && !empty($report['Topic']['id'])) {
							echo $this->Html->link($report['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $report['Topic']['slug'], 'admin' => false));

						} else if ($report['Report']['itemType'] == Report::USER && !empty($report['User']['id'])) {
							echo $this->Html->link($report['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['User']['id'], 'admin' => true));

						} else if ($report['Report']['itemType'] == Report::POST && !empty($report['Post']['id'])) {
							echo h($report['Post']['content']);

						} else {
							echo '<em class="gray">(' . __d('forum', 'Deleted') . ')</em>';
						} ?>
					</td>
					<td><?php echo $this->Html->link($report['Reporter'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['id'], 'admin' => true)); ?></td>
					<td><?php echo h($report['Report']['comment']); ?></td>
					<td><?php echo $this->Time->nice($report['Report']['created'], $this->Common->timezone()); ?></td>
				</tr>

				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<?php }

if ($latestUsers) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Latest Users'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Username'); ?></th>
					<th><?php echo __d('forum', 'Email'); ?></th>
					<th><?php echo __d('forum', 'Joined'); ?></th>
					<th><?php echo __d('forum', 'Topics'); ?></th>
					<th><?php echo __d('forum', 'Posts'); ?></th>
					<th><?php echo __d('forum', 'Options'); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($latestUsers as $counter => $latest) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($latest['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $latest['Profile']['id'], 'admin' => true)); ?></td>
					<td><?php echo $latest['User'][$config['userMap']['email']]; ?></td>
					<td class="created"><?php echo $this->Time->nice($latest['Profile']['created'], $this->Common->timezone()); ?></td>
					<td class="stat"><?php echo number_format($latest['Profile']['totalTopics']); ?></td>
					<td class="stat"><?php echo number_format($latest['Profile']['totalPosts']); ?></td>
					<td class="align-center">
						<?php echo $this->Html->link(__d('forum', 'Edit'), array('controller' => 'users', 'action' => 'edit', $latest['Profile']['id'], 'admin' => true)); ?>
					</td>
				</tr>

				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<?php } ?>
