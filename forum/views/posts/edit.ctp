
<?php // Crumbs
$html->addCrumb($post['Topic']['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($post['Topic']['ForumCategory']['Parent']['id'])) {
	$html->addCrumb($post['Topic']['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $post['Topic']['ForumCategory']['Parent']['id']));
}
$html->addCrumb($post['Topic']['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $post['Topic']['ForumCategory']['id']));
$html->addCrumb($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['id'])); ?>

<h2><?php __d('forum', 'Edit Post'); ?></h2>

<?php echo $form->create('Post'); ?>
<?php echo $form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => __d('forum', 'Content', true))); ?>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $form->end(__d('forum', 'Update', true)); ?>