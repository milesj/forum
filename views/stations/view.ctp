<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="title">
	<h2><?php echo $forum['Forum']['title']; ?></h2>
	
	<?php if (!empty($forum['Forum']['description'])) { ?>
		<p><?php echo $forum['Forum']['description']; ?></p>
	<?php } ?>
</div>

<?php if (!empty($forum['SubForum'])) { ?>

	<div class="container">
		<div class="containerHeader">
			<a href="javascript:;" onclick="return Forum.toggleForums(this, <?php echo $forum['Forum']['id']; ?>);" class="toggle">-</a>
			<h3><?php __d('forum', 'Sub-Forums'); ?></h3>
		</div>
		
		<div class="containerContent" id="forums-<?php echo $forum['Forum']['id']; ?>">
			<table cellspacing="0" class="table">
				<thead>
					<tr>
						<th colspan="2"><?php __d('forum', 'Forum'); ?></th>
						<th><?php __d('forum', 'Topics'); ?></th>
						<th><?php __d('forum', 'Posts'); ?></th>
						<th><?php __d('forum', 'Activity'); ?></th>
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
						<th colspan="2"><?php echo $this->Paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
						<th><?php echo $this->Paginator->sort(__d('forum', 'Author', true), 'User.'. $config['userMap']['username']); ?></th>
						<th><?php echo $this->Paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
						<th><?php echo $this->Paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
						<th><?php echo $this->Paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
						<th><?php echo $this->Paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php if (!empty($stickies)) { ?>

					<tr class="headRow">
						<td colspan="7" class="dark"><?php __d('forum', 'Important Topics'); ?></td>
					</tr>

					<?php foreach ($stickies as $counter => $topic) {
						echo $this->element('tiles/topic_row', array(
							'counter' => $counter,
							'topic' => $topic
						));
					} ?>

					<tr class="headRow">
						<td colspan="7" class="dark"><?php __d('forum', 'Regular Topics'); ?></td>
					</tr>

				<?php }

				if (!empty($topics)) {
					foreach ($topics as $counter => $topic) {
						echo $this->element('tiles/topic_row', array(
							'counter' => $counter,
							'topic' => $topic
						));
					}
				} else { ?>

					<tr>
						<td colspan="7" class="empty"><?php __d('forum', 'There are no topics within this forum category.'); ?></td>
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
		
		if (!empty($forum['Moderator'])) {
			foreach ($forum['Moderator'] as $mod) {
				$moderators[] = $this->Html->link($mod['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $mod['User']['id'])); 
			}	
		} ?>

		<table class="table">
			<tbody>
				<tr>
					<td class="align-right"><?php __d('forum', 'Total Topics'); ?>: </td>
					<td><strong><?php echo $forum['Forum']['topic_count']; ?></strong></td>

					<td class="align-right"><?php __d('forum', 'Increases Post Count'); ?>: </td>
					<td><strong><?php __d('forum', $forum['Forum']['settingPostCount'] ? 'Yes' : 'No'); ?></strong></td>

					<td class="align-right"><?php __d('forum', 'Can Read Topics'); ?>: </td>
					<td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessRead']) ? 'Yes' : 'No'); ?></strong></td>

					<td class="align-right"><?php __d('forum', 'Can Create Topics'); ?>: </td>
					<td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessPost']) ? 'Yes' : 'No'); ?></strong></td>
				</tr>
				<tr>
					<td class="align-right"><?php __d('forum', 'Total Posts'); ?>: </td>
					<td><strong><?php echo $forum['Forum']['post_count']; ?></strong></td>

					<td class="align-right"><?php __d('forum', 'Auto-Lock Topics'); ?>: </td>
					<td><strong><?php __d('forum', $forum['Forum']['settingAutoLock'] ? 'Yes' : 'No'); ?></strong></td>

					<td class="align-right"><?php __d('forum', 'Can Reply'); ?>: </td>
					<td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessReply']) ? 'Yes' : 'No'); ?></strong></td>

					<td class="align-right"><?php __d('forum', 'Can Create Polls'); ?>: </td>
					<td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessPoll']) ? 'Yes' : 'No'); ?></strong></td>
				</tr>
				<?php if (!empty($moderators)) { ?>
					<tr>
						<td class="align-right"><?php __d('forum', 'Moderators'); ?>: </td>
						<td colspan="7"><?php echo implode(', ', $moderators); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>

<?php } ?>