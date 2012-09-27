<?php
$pages = $this->Common->topicPages($topic['Topic']);
$columns = isset($columns) ? $columns : array(); ?>

<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
	<?php if (in_array('status', $columns)) { ?>
		<td class="icon"><input type="checkbox" name="data[Topic][items][]" value="<?php echo $topic['Topic']['id']; ?>" /></td>
	<?php } else { ?>
		<td class="icon"><?php echo $this->Common->topicIcon($topic); ?></td>
	<?php } ?>
	<td>
		<?php if (!empty($topic['Poll']['id'])) {
			echo $this->Html->image('/forum/img/poll.png', array('alt' => 'Poll'));
		} ?>

		<strong><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></strong>

		<?php if (count($pages) > 1) { ?>
		<br /><span class="gray"><?php echo __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</span>
		<?php } ?>
	</td>
	<?php if (in_array('forum', $columns)) { ?>
		<td class="parent"><?php echo $this->Html->link($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug'])); ?></td>
	<?php } ?>
	<?php if (in_array('status', $columns)) { ?>
		<td class="status"><?php echo $this->Common->options('topicStatus', $topic['Topic']['status']); ?></td>
	<?php } ?>
	<td class="author">
		<?php echo $this->Html->link($topic['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['User']['id'])); ?>
	</td>
	<td class="created">
		<?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Common->timezone()); ?>
	</td>
	<td class="stat">
		<?php echo number_format($topic['Topic']['post_count']); ?>
	</td>
	<td class="stat">
		<?php echo number_format($topic['Topic']['view_count']); ?>
	</td>
	<td class="activity">
		<?php if (!empty($topic['LastPost']['id'])) {
			echo $this->Time->timeAgoInWords($topic['LastPost']['created'], array('userOffset' => $this->Common->timezone())); ?>

			<?php if (!empty($topic['LastUser']['id'])) { ?>
				<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['LastUser'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
			<?php } ?>

			<?php echo $this->Html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], 'page' => $topic['Topic']['page_count'], '#' => 'post-' . $topic['Topic']['lastPost_id']))); ?>
		<?php } else { ?>
			<em class="gray"><?php echo __d('forum', 'No latest activity to display'); ?></em>
		<?php } ?>
	</td>
</tr>
