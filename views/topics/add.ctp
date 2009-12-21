
<?php // Crumbs
$html->addCrumb($category['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($category['Parent']['id'])) {
	$html->addCrumb($category['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $category['Parent']['id']));
}
$html->addCrumb($category['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $category['ForumCategory']['id'])); ?>

<h2><?php echo $pageTitle; ?></h2>

<?php echo $form->create('Topic', array('url' => array('controller' => 'topics', 'action' => 'add', $id, $type))); ?>
<?php echo $form->input('title', array('label' => __d('forum', 'Title', true))); ?>
<?php echo $form->input('forum_category_id', array('options' => $forums, 'escape' => false, 'empty' => '-- '. __d('forum', 'Select a Forum', true) .' --', 'label' => __d('forum', 'Forum Category', true))); ?>

<?php if ($cupcake->hasAccess('super', $category['ForumCategory']['id'])) {
	echo $form->input('status', array('options' => $cupcake->options(2), 'label' => __d('forum', 'Status', true)));
	echo $form->input('type', array('options' => array(
		0 => __d('forum', 'Normal', true),
		1 => __d('forum', 'Sticky', true),
		2 => __d('forum', 'Important', true),
		3 => __d('forum', 'Announcement', true)
	), 'label' => __d('forum', 'Type', true)));
} ?>

<?php if ($type == 'poll') {
	echo $form->input('options', array('label' => __d('forum', 'Poll Options', true), 'type' => 'textarea', 'label' => __d('forum', 'Poll Options', true), 'after' => '<div class="inputText">'. __d('forum', 'One option per line. Max 10 options', true) .'</div>'));
	echo $form->input('expires', array('label' => __d('forum', 'Expiration Date', true), 'after' => ' '. __d('forum', 'How many days till expiration? Leave blank to last forever.', true), 'style' => 'width: 50px'));
} ?>

<?php echo $form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => __d('forum', 'Content', true))); ?>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $form->end(__d('forum', 'Post', true)); ?>