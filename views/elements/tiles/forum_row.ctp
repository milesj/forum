
<?php 
$forum = isset($forum['Forum']) ? $forum['Forum'] : $forum;
$subForums = array();

if (!empty($forum['SubForum'])) {
	foreach ($forum['SubForum'] as $sub) {
		$subForums[] = $this->Html->link($sub['title'], array('controller' => 'stations', 'action' => 'view', $sub['slug']));
	}
} ?>

<tr id="category_<?php echo $forum['id']; ?>"<?php if ($counter % 2) echo ' class="altRow"'; ?>>
	<td class="ac" style="width: 35px"><?php echo $this->Common->forumIcon($forum); ?></td>
	<td>
		<strong><?php echo $this->Html->link($forum['title'], array('controller' => 'stations', 'action' => 'view', $forum['slug'])); ?></strong><br />
		<?php echo $forum['description']; ?>

		<?php if (!empty($subForums)) { ?>
			<div class="subForums">
				<span class="gray"><?php __d('forum', 'Sub-Forums'); ?>:</span> <?php echo implode(', ', $subForums); ?>
			</div>     
		<?php } ?>
	</td>
	<td class="ac"><?php echo number_format($forum['topic_count']); ?></td>
	<td class="ac"><?php echo number_format($forum['post_count']); ?></td>
	<td>
		<?php // Last activity
		if (!empty($forum['LastTopic']['id'])) {
			$lastTime = !empty($forum['LastPost']['created']) ? $forum['LastPost']['created'] : $forum['LastTopic']['created']; ?>

			<?php echo $this->Html->link($forum['LastTopic']['title'], array('controller' => 'topics', 'action' => 'view', $forum['LastTopic']['slug'])); ?>
			<?php echo $this->Html->image('/forum/img/goto.png', array('alt' => '', 'url' => array('controller' => 'topics', 'action' => 'view', $forum['LastTopic']['slug'], 'page' => $forum['LastTopic']['page_count'], '#' => 'post_'. $forum['lastPost_id']))); ?><br />

			<em><?php echo $this->Time->relativeTime($lastTime, array('userOffset' => $this->Common->timezone())); ?></em> 
			<span class="gray"><?php __d('forum', 'by'); ?> <?php echo $this->Html->link($forum['LastUser']['username'], array('controller' => 'users', 'action' => 'profile', $forum['lastUser_id'])); ?></span>
		<?php } else {
			__d('forum', 'No latest activity to display');
		} ?>
	</td>
</tr>