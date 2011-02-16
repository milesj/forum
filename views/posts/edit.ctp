
<?php // Crumbs
$this->Html->addCrumb($post['Topic']['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($post['Topic']['ForumCategory']['Parent']['slug'])) {
	$this->Html->addCrumb($post['Topic']['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $post['Topic']['ForumCategory']['Parent']['slug']));
}
$this->Html->addCrumb($post['Topic']['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $post['Topic']['ForumCategory']['slug']));
$this->Html->addCrumb($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug'])); ?>

<div class="forumHeader">
	<h2><?php __d('forum', 'Edit Post'); ?></h2>
</div>

<?php echo $this->Form->create('Post'); ?>

<div class="input textarea">
	<?php echo $this->Form->label('content', __d('forum', 'Content', true)); ?>

	<div id="textarea">
		<?php echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => false, 'div' => false)); ?>
	</div>

	<span class="clear"><!-- --></span>
	<?php echo $this->element('markitup', array('textarea' => 'PostContent')); ?>
</div>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $this->Form->end(__d('forum', 'Update', true)); ?>