
<?php // Crumbs
$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="forumHeader">
	<h2><?php echo $pageTitle; ?></h2>
</div>

<?php 
echo $this->Form->create('Topic', array('url' => $this->here));
echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));
echo $this->Form->input('forum_id', array('options' => $forums, 'escape' => false, 'empty' => '-- '. __d('forum', 'Select a Forum', true) .' --', 'label' => __d('forum', 'Forum', true)));

if ($this->Common->hasAccess('super', $forum['Forum']['id'])) {
	echo $this->Form->input('status', array('options' => $this->Common->options('topicStatus'), 'label' => __d('forum', 'Status', true)));
	echo $this->Form->input('type', array('options' => $this->Common->options('topicTypes'), 'label' => __d('forum', 'Type', true)));
} 

if ($type == 'poll') {
	echo $this->Form->input('options', array('label' => __d('forum', 'Poll Options', true), 'type' => 'textarea', 'label' => __d('forum', 'Poll Options', true), 'after' => '<div class="inputText">'. __d('forum', 'One option per line. Max 10 options', true) .'</div>'));
	echo $this->Form->input('expires', array('label' => __d('forum', 'Expiration Date', true), 'after' => ' '. __d('forum', 'How many days till expiration? Leave blank to last forever.', true), 'style' => 'width: 50px'));
} ?>

<div class="input textarea">
	<?php echo $this->Form->label('content', __d('forum', 'Content', true)); ?>
	
	<div id="textarea">
		<?php echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => false, 'div' => false)); ?>
	</div>

	<span class="clear"><!-- --></span>
	<?php echo $this->element('markitup', array('textarea' => 'TopicContent')); ?>
</div>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $this->Form->end(__d('forum', 'Post', true)); ?>
