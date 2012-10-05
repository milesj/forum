<?php
$forum = isset($forum['Forum']) ? $forum['Forum'] : $forum;
$subForums = array();

if ($forum['SubForum']) {
	foreach ($forum['SubForum'] as $sub) {
		$subForums[] = $this->Html->link($sub['title'], array('controller' => 'stations', 'action' => 'view', $sub['slug']));
	}
} ?>

<tr id="forum-<?php echo $forum['id']; ?>"<?php if ($counter % 2) echo ' class="altRow"'; ?>>
	<td class="icon">
		<?php echo $this->Common->forumIcon($forum); ?>
	</td>
	<td>
		<strong><?php echo $this->Html->link($forum['title'], array('controller' => 'stations', 'action' => 'view', $forum['slug'])); ?></strong><br />
		<?php echo h($forum['description']); ?>

		<?php if ($subForums) { ?>
			<div class="subForums">
				<span class="gray"><?php echo __d('forum', 'Sub-Forums'); ?>:</span> <?php echo implode(', ', $subForums); ?>
			</div>
		<?php } ?>
	</td>
	<td class="stat"><?php echo number_format($forum['topic_count']); ?></td>
	<td class="stat"><?php echo number_format($forum['post_count']); ?></td>
	<td class="activity">
		<?php if (!empty($forum['LastTopic']['id'])) {
			$lastTime = isset($forum['LastPost']['created']) ? $forum['LastPost']['created'] : $forum['LastTopic']['modified'];

			echo $this->Html->link($forum['LastTopic']['title'], array('controller' => 'topics', 'action' => 'view', $forum['LastTopic']['slug'])) . ' ';
			echo $this->Html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $forum['LastTopic']['slug'], 'page' => $forum['LastTopic']['page_count'], '#' => 'post-' . $forum['lastPost_id']))); ?><br />

			<em><?php echo $this->Time->timeAgoInWords($lastTime, array('userOffset' => $this->Common->timezone())); ?></em>

			<?php if (!empty($forum['LastUser']['id'])) { ?>
				<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($forum['LastUser'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $forum['lastUser_id'])); ?></span>
			<?php }
		} else { ?>
			<em class="gray"><?php echo __d('forum', 'No latest activity to display'); ?></em>
		<?php } ?>
	</td>
</tr>