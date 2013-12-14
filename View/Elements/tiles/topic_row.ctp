<?php
$pages = $this->Forum->topicPages($topic['Topic']);
$columns = isset($columns) ? $columns : array(); ?>

<tr>
	<?php if (in_array('status', $columns)) { ?>
		<td class="col-icon"><input type="checkbox" name="data[Topic][items][]" value="<?php echo $topic['Topic']['id']; ?>"></td>
	<?php } else { ?>
		<td class="col-icon"><?php echo $this->Forum->topicIcon($topic); ?></td>
	<?php } ?>
	<td>
		<?php if (!empty($topic['Poll']['id'])) {
			echo $this->Html->tag('span', '', array('class' => 'fa fa-bar-chart-o js-tooltip float-right', 'data-tooltip' => __d('forum', 'Poll')));
		} ?>

		<?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']), array('class' => 'topic-title'));

		if (count($pages) > 1) { ?>
			<div class="topic-pages"><?php echo __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</div>
		<?php } ?>
	</td>
	<?php if (in_array('forum', $columns)) { ?>
		<td class="col-parent"><?php echo $this->Html->link($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug'])); ?></td>
	<?php } ?>
	<?php if (in_array('status', $columns)) { ?>
		<td class="col-status"><?php echo $this->Utility->enum('Forum.Topic', 'status', $topic['Topic']['status']); ?></td>
	<?php } ?>
	<td class="col-author">
		<?php echo $this->Html->link($topic['User'][$userFields['username']], $this->Forum->profileUrl($topic['User'])); ?>
	</td>
	<td class="col-created">
		<?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Forum->timezone()); ?>
	</td>
	<td class="col-stat">
		<?php echo number_format($topic['Topic']['post_count']); ?>
	</td>
	<td class="col-stat">
		<?php echo number_format($topic['Topic']['view_count']); ?>
	</td>
	<td class="col-activity">
		<?php if (!empty($topic['LastPost']['id'])) {
			echo $this->Time->timeAgoInWords($topic['LastPost']['created'], array('timezone' => $this->Forum->timezone())); ?>

			<?php if (!empty($topic['LastUser']['id'])) { ?>
				<span class="text-muted"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['LastUser'][$userFields['username']], $this->Forum->profileUrl($topic['LastUser'])); ?></span>
			<?php } ?>

			<?php echo $this->Html->link('<span class="fa fa-external-link"></span>', array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], 'page' => $topic['Topic']['page_count'], '#' => 'post-' . $topic['Topic']['lastPost_id']), array('escape' => false)); ?>
		<?php } else { ?>
			<em class="text-muted"><?php echo __d('forum', 'No latest activity to display'); ?></em>
		<?php } ?>
	</td>
</tr>
