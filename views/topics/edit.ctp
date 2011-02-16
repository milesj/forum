
<?php // Crumbs
$this->Html->addCrumb($topic['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($topic['ForumCategory']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['Parent']['slug']));
}
$this->Html->addCrumb($topic['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="forumHeader">
	<h2><?php __d('forum', 'Edit Topic'); ?></h2>
</div>

<?php echo $this->Form->create('Topic', array('url' => '/forum/topics/edit/'. $id)); ?>
<?php echo $this->Form->input('title', array('label' => __d('forum', 'Title', true))); ?>

<?php if ($this->Cupcake->hasAccess('super', $topic['ForumCategory']['id'])) {
	echo $this->Form->input('forum_category_id', array('label' => __d('forum', 'Forum Category', true), 'options' => $forums, 'escape' => false, 'empty' => '-- '. __d('forum', 'Select a Forum', true) .' --'));
	echo $this->Form->input('status', array('label' => __d('forum', 'Status', true), 'options' => $this->Cupcake->options(2)));
	echo $this->Form->input('type', array('options' => array(
		0 => __d('forum', 'Normal', true),
		1 => __d('forum', 'Sticky', true),
		2 => __d('forum', 'Important', true),
		3 => __d('forum', 'Announcement', true)
	), 'label' => __d('forum', 'Type', true)));
} else {
	echo $this->Form->input('forum_category_id', array('type' => 'hidden'));
} ?>

<?php // Has a poll?
if (!empty($topic['Poll']['id'])) { ?>
<div class="input poll">
	<?php echo $this->Form->label('Poll.id', __d('forum', 'Poll Options', true));
	echo $this->Form->input('Poll.id', array('type' => 'hidden')); ?>
    
    <table>
	<?php foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>
    <tr>
    	<td><?php echo $this->Form->input('Poll.PollOption.'. $row .'.id', array('type' => 'hidden')); echo $row + 1; ?>)</td>
    	<td><?php echo $this->Form->input('Poll.PollOption.'. $row .'.option', array('div' => false, 'label' => false, 'style' => 'width: 300px')); ?></td>
        <td style="width: 100px"><?php echo $this->Form->input('Poll.PollOption.'. $row .'.delete', array('type' => 'checkbox', 'div' => false, 'label' => false, 'value' => 0)); ?> <?php __d('forum', 'Delete'); ?>?</td>
   	</tr>
	<?php } ?>
    </table>
</div>

<?php echo $this->Form->input('Poll.expires', array('label' => __d('forum', 'Expiration Date', true), 'type' => 'text', 'after' => ' '. __d('forum', 'How many days till expiration? Leave blank to last forever.', true), 'style' => 'width: 50px'));
} ?>

<?php echo $this->Form->input('FirstPost.id', array('type' => 'hidden')); ?>

<div class="input textarea">
	<?php echo $this->Form->label('content', __d('forum', 'Content', true)); ?>

	<div id="textarea">
		<?php echo $this->Form->input('FirstPost.content', array('type' => 'textarea', 'rows' => 15, 'label' => false, 'div' => false)); ?>
	</div>

	<span class="clear"><!-- --></span>
	<?php echo $this->element('markitup', array('textarea' => 'FirstPostContent')); ?>
</div>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $this->Form->end(__d('forum', 'Update', true)); ?>