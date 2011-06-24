
<div class="forumHeader">
	<h2><?php __d('forum', 'Delete Forum'); ?></h2>
</div>

<?php if (empty($subForums)) { ?>
	<p><?php __d('forum', 'You may not delete this forum, you must have at least one forum active.'); ?></p>

<?php } else { ?>
	<p><?php printf(__d('forum', 'Before you delete the %s forum, please select which forum all topics and sub-forums should be moved to.', true), '<strong>'. $forum['Forum']['title'] .'</strong>'); ?></p>

	<?php echo $this->Form->create('Forum', array('url' => $this->here));
	echo $this->Form->input('move_topics', array('options' => $topicForums, 'escape' => false, 'label' => __d('forum', 'Move Topics To', true)));
	echo $this->Form->input('move_forums', array('options' => $subForums, 'escape' => false, 'label' => __d('forum', 'Move Sub-forums To', true)));
	echo $this->Form->end(__d('forum', 'Delete', true));
} ?>