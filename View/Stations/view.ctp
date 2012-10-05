<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="title">
	<h2><?php echo h($forum['Forum']['title']); ?></h2>

	<?php if ($forum['Forum']['description']) { ?>
		<p><?php echo h($forum['Forum']['description']); ?></p>
	<?php } ?>
</div>

<?php if ($forum['SubForum']) { ?>

	<div class="container">
		<div class="containerHeader">
			<a href="javascript:;" onclick="return Forum.toggleForums(this, <?php echo $forum['Forum']['id']; ?>);" class="toggle">-</a>
			<h3><?php echo __d('forum', 'Sub-Forums'); ?></h3>
		</div>

		<div class="containerContent" id="forums-<?php echo $forum['Forum']['id']; ?>">
			<table cellspacing="0" class="table">
				<thead>
					<tr>
						<th colspan="2"><?php echo __d('forum', 'Forum'); ?></th>
						<th><?php echo __d('forum', 'Topics'); ?></th>
						<th><?php echo __d('forum', 'Posts'); ?></th>
						<th><?php echo __d('forum', 'Activity'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($forum['SubForum'] as $counter => $subForum) {
						echo $this->element('tiles/forum_row', array(
							'forum' => $subForum,
							'counter' => $counter
						));
					} ?>
				</tbody>
			</table>
		</div>
	</div>

<?php }

// Cant post in top level
if ($forum['Forum']['forum_id'] > 0) {
	echo $this->element('tiles/forum_controls', array(
		'forum' => $forum
	)); ?>

	<div class="container" id="topics">
		<div class="containerContent">
			<?php echo $this->element('pagination'); ?>

			<table class="table topics">
				<thead>
					<tr>
						<th colspan="2"><?php echo $this->Paginator->sort('Topic.title', __d('forum', 'Topic')); ?></th>
						<th><?php echo $this->Paginator->sort('User.' . $config['userMap']['username'], __d('forum', 'Author')); ?></th>
						<th><?php echo $this->Paginator->sort('Topic.created', __d('forum', 'Created')); ?></th>
						<th><?php echo $this->Paginator->sort('Topic.post_count', __d('forum', 'Posts')); ?></th>
						<th><?php echo $this->Paginator->sort('Topic.view_count', __d('forum', 'Views')); ?></th>
						<th><?php echo $this->Paginator->sort('LastPost.created', __d('forum', 'Activity')); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php if ($stickies) { ?>

					<tr class="headRow">
						<td colspan="7" class="dark"><?php echo __d('forum', 'Important Topics'); ?></td>
					</tr>

					<?php foreach ($stickies as $counter => $topic) {
						echo $this->element('tiles/topic_row', array(
							'counter' => $counter,
							'topic' => $topic
						));
					} ?>

					<tr class="headRow">
						<td colspan="7" class="dark"><?php echo __d('forum', 'Regular Topics'); ?></td>
					</tr>

				<?php }

				if ($topics) {
					foreach ($topics as $counter => $topic) {
						echo $this->element('tiles/topic_row', array(
							'counter' => $counter,
							'topic' => $topic
						));
					}
				} else { ?>

					<tr>
						<td colspan="7" class="empty"><?php echo __d('forum', 'There are no topics within this forum.'); ?></td>
					</tr>

				<?php } ?>

				</tbody>
			</table>

			<?php echo $this->element('pagination'); ?>
		</div>
	</div>

	<?php echo $this->element('tiles/forum_controls', array(
		'forum' => $forum
	)); ?>

	<div class="statistics">
		<?php $moderators = array();

		if ($forum['Moderator']) {
			foreach ($forum['Moderator'] as $mod) {
				$moderators[] = $this->Html->link($mod['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $mod['User']['id']));
			}
		} ?>

		<table class="table">
			<tbody>
				<tr>
					<td class="align-right"><?php echo __d('forum', 'Total Topics'); ?>: </td>
					<td><strong><?php echo $forum['Forum']['topic_count']; ?></strong></td>

					<td class="align-right"><?php echo __d('forum', 'Increases Post Count'); ?>: </td>
					<td><strong><?php echo $forum['Forum']['settingPostCount'] ? __d('forum', 'Yes') : __d('forum', 'No'); ?></strong></td>

					<td class="align-right"><?php echo __d('forum', 'Can Read Topics'); ?>: </td>
					<td><strong><?php echo $this->Common->hasAccess($forum['Forum']['accessRead']) ? __d('forum', 'Yes') : __d('forum', 'No'); ?></strong></td>

					<td class="align-right"><?php echo __d('forum', 'Can Create Topics'); ?>: </td>
					<td><strong><?php echo $this->Common->hasAccess($forum['Forum']['accessPost']) ? __d('forum', 'Yes') : __d('forum', 'No'); ?></strong></td>
				</tr>
				<tr>
					<td class="align-right"><?php echo __d('forum', 'Total Posts'); ?>: </td>
					<td><strong><?php echo $forum['Forum']['post_count']; ?></strong></td>

					<td class="align-right"><?php echo __d('forum', 'Auto-Lock Topics'); ?>: </td>
					<td><strong><?php echo $forum['Forum']['settingAutoLock'] ? __d('forum', 'Yes') : __d('forum', 'No'); ?></strong></td>

					<td class="align-right"><?php echo __d('forum', 'Can Reply'); ?>: </td>
					<td><strong><?php echo $this->Common->hasAccess($forum['Forum']['accessReply']) ? __d('forum', 'Yes') : __d('forum', 'No'); ?></strong></td>

					<td class="align-right"><?php echo __d('forum', 'Can Create Polls'); ?>: </td>
					<td><strong><?php echo $this->Common->hasAccess($forum['Forum']['accessPoll']) ? __d('forum', 'Yes') : __d('forum', 'No'); ?></strong></td>
				</tr>
				<?php if ($moderators) { ?>
					<tr>
						<td class="align-right"><?php echo __d('forum', 'Moderators'); ?>: </td>
						<td colspan="7"><?php echo implode(', ', $moderators); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

<?php } ?>