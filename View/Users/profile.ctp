<?php

$this->Breadcrumb->add(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Breadcrumb->add($profile['User'][$config['userMap']['username']], $this->Forum->profileUrl($profile['User'])); ?>

<div class="title">
	<?php echo $this->Html->link(__d('forum', 'Report User'), array('action' => 'report', $profile['User']['id']), array('class' => 'button float-right')); ?>
	<h2><?php echo h($profile['User'][$config['userMap']['username']]); ?></h2>
</div>

<?php if (!empty($profile['Profile']['signature'])) {
	echo $this->Decoda->parse($profile['Profile']['signature']);
} ?>

<div class="container">
	<table class="table">
		<tbody>
			<tr>
				<?php if ($avatar = $this->Forum->avatar($profile)) { ?>
					<td rowspan="2" style="width: 80px;">
						<?php echo $avatar; ?>
					</td>
				<?php } ?>

				<td><strong><?php echo __d('forum', 'Joined'); ?>:</strong></td>
				<td><?php echo $this->Time->nice($profile['Profile']['created'], $this->Forum->timezone()); ?></td>

				<td><strong><?php echo __d('forum', 'Total Topics'); ?>:</strong></td>
				<td><?php echo number_format($profile['Profile']['totalTopics']); ?></td>

				<td><strong><?php echo __d('forum', 'Roles'); ?>:</strong></td>
				<td><?php // @TODO ?></td>
			</tr>
			<tr>
				<td><strong><?php echo __d('forum', 'Last Login'); ?>:</strong></td>
				<td>
					<?php if ($profile['Profile']['lastLogin']) {
						echo $this->Time->timeAgoInWords($profile['Profile']['lastLogin'], array('userOffset' => $this->Forum->timezone()));
					} else {
						echo '<em>' . __d('forum', 'Never') . '</em>';
					} ?>
				</td>

				<td><strong><?php echo __d('forum', 'Total Posts'); ?>:</strong></td>
				<td><?php echo number_format($profile['Profile']['totalPosts']); ?></td>

				<td><strong><?php echo __d('forum', 'Moderates'); ?>:</strong></td>
				<td>
					<?php if (!empty($profile['User']['ForumModerator'])) {
						$mods = array();
						foreach ($profile['User']['ForumModerator'] as $mod) {
							$mods[] = $this->Html->link($mod['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $mod['Forum']['slug']));
						}
						echo implode(', ', $mods);
					} else {
						echo '<em>' . __d('forum', 'N/A') . '</em>';
					} ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php if ($topics) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Latest Topics'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Topic'); ?></th>
					<th><?php echo __d('forum', 'Created'); ?></th>
					<th><?php echo __d('forum', 'Posts'); ?></th>
					<th><?php echo __d('forum', 'Views'); ?></th>
					<th><?php echo __d('forum', 'Last Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($topics as $counter => $topic) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Forum->timezone()); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="activity"><?php echo $this->Time->timeAgoInWords($topic['LastPost']['created'], array('userOffset' => $this->Forum->timezone())); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php }

if ($posts) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Latest Posts'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Topic'); ?></th>
					<th><?php echo __d('forum', 'Author'); ?></th>
					<th><?php echo __d('forum', 'Posted On'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach($posts as $post) { ?>

				<tr class="altRow">
					<td><strong><?php echo $this->Html->link($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug'])); ?></strong></td>
					<td><?php echo $this->Html->link($post['Topic']['User'][$config['userMap']['username']], $this->Forum->profileUrl($post['Topic']['User'])); ?></td>
					<td class="ar"><?php echo $this->Time->timeAgoInWords($post['Post']['created'], array('userOffset' => $this->Forum->timezone())); ?></td>
				</tr>
				<tr>
					<td colspan="3"><?php echo $this->Decoda->parse($post['Post']['content']); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php } ?>