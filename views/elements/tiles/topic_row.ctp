
<?php 
$pages = $this->Common->topicPages($topic['Topic']);
$moderate = isset($moderate) ? true : false; ?>

<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
	<?php if ($moderate) { ?>
		<td class="ac"><input type="checkbox" name="data[Topic][items][]" value="<?php echo $topic['Topic']['id']; ?>" /></td>
	<?php } else { ?>
		<td class="ac" style="width: 35px"><?php echo $this->Common->topicIcon($topic); ?></td>
	<?php } ?>
	<td>
		<?php if (!empty($topic['Poll']['id'])) { 
			echo $this->Html->image('/forum/img/poll.png', array('alt' => 'Poll'));
		} ?>

		<strong><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></strong>

		<?php if (count($pages) > 1) { ?>
		<br /><span class="gray"><?php __d('forum', 'Pages'); ?>: [ <?php echo implode(', ', $pages); ?> ]</span>
		<?php } ?>
	</td>
	<?php if ($moderate) { ?>
		<td class="ac"><?php echo $this->Common->options(2, $topic['Topic']['status']); ?></td>
	<?php } ?>
	<td class="ac"><?php echo $this->Html->link($topic['User']['username'], array('controller' => 'users', 'action' => 'profile', $topic['User']['id'])); ?></td>
	<td class="ac"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Common->timezone()); ?></td>
	<td class="ac"><?php echo number_format($topic['Topic']['post_count']); ?></td>
	<td class="ac"><?php echo number_format($topic['Topic']['view_count']); ?></td>
	<td>
		<?php // Last activity
		if (!empty($topic['LastPost']['id'])) {
			$lastTime = !empty($topic['LastPost']['created']) ? $topic['LastPost']['created'] : $topic['Topic']['modified']; ?>

			<em><?php echo $this->Time->relativeTime($lastTime, array('userOffset' => $this->Common->timezone())); ?></em><br />
			<span class="gray"><?php __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['LastUser']['username'], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
			<?php echo $this->Html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], 'page' => $topic['Topic']['page_count'], '#' => 'post_'. $topic['Topic']['lastPost_id']))); ?>
		<?php } else {
			__d('forum', 'No latest activity to display');
		} ?>
	</td>
</tr>
