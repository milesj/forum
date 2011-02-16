
<?php // Crumbs
$this->Html->addCrumb($category['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($category['Parent']['slug'])) {
	$this->Html->addCrumb($category['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $category['Parent']['slug']));
}
$this->Html->addCrumb($category['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $category['ForumCategory']['slug'])); ?>

<div class="forumHeader">
	<h2><?php echo $pageTitle; ?></h2>
</div>

<?php echo $this->Form->create('Topic', array('url' => array('controller' => 'topics', 'action' => 'add', $id, $type))); ?>
<?php echo $this->Form->input('title', array('label' => __d('forum', 'Title', true))); ?>
<?php echo $this->Form->input('forum_category_id', array('options' => $forums, 'escape' => false, 'empty' => '-- '. __d('forum', 'Select a Forum', true) .' --', 'label' => __d('forum', 'Forum Category', true))); ?>

<?php if ($this->Cupcake->hasAccess('super', $category['ForumCategory']['id'])) {
	echo $this->Form->input('status', array('options' => $this->Cupcake->options(2), 'label' => __d('forum', 'Status', true)));
	echo $this->Form->input('type', array('options' => array(
		0 => __d('forum', 'Normal', true),
		1 => __d('forum', 'Sticky', true),
		2 => __d('forum', 'Important', true),
		3 => __d('forum', 'Announcement', true)
	), 'label' => __d('forum', 'Type', true)));
} ?>

<?php if ($type == 'poll') {
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
