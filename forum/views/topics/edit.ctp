
<?php // Crumbs
$html->addCrumb($topic['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($topic['ForumCategory']['Parent']['id'])) {
	$html->addCrumb($topic['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['Parent']['id']));
}
$html->addCrumb($topic['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['id']));
$html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'])); ?>

<h2><?php __d('forum', 'Edit Topic'); ?></h2>

<?php echo $form->create('Topic', array('url' => '/forum/topics/edit/'. $id)); ?>
<?php echo $form->input('title', array('label' => __d('forum', 'Title', true))); ?>

<?php if ($cupcake->hasAccess('super', $topic['ForumCategory']['id'])) {
	echo $form->input('forum_category_id', array('label' => __d('forum', 'Forum Category', true), 'options' => $forums, 'escape' => false, 'empty' => '-- '. __d('forum', 'Select a Forum', true) .' --'));
	echo $form->input('status', array('label' => __d('forum', 'Status', true), 'options' => $cupcake->options(2)));
	echo $form->input('type', array('options' => array(
		0 => __d('forum', 'Normal', true),
		1 => __d('forum', 'Sticky', true),
		2 => __d('forum', 'Important', true),
		3 => __d('forum', 'Announcement', true)
	), 'label' => __d('forum', 'Type', true)));
} else {
	echo $form->input('forum_category_id', array('type' => 'hidden'));
} ?>

<?php // Has a poll?
if (!empty($topic['Poll']['id'])) { ?>
<div class="input poll">
	<?php echo $form->label('Poll.id', __d('forum', 'Poll Options', true));
	echo $form->input('Poll.id', array('type' => 'hidden')); ?>
    
    <table>
	<?php foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>
    <tr>
    	<td><?php echo $form->input('Poll.PollOption.'. $row .'.id', array('type' => 'hidden')); echo $row + 1; ?>)</td>
    	<td><?php echo $form->input('Poll.PollOption.'. $row .'.option', array('div' => false, 'label' => false, 'style' => 'width: 300px')); ?></td>
        <td style="width: 100px"><?php echo $form->input('Poll.PollOption.'. $row .'.delete', array('type' => 'checkbox', 'div' => false, 'label' => false, 'value' => 0)); ?> <?php __d('forum', 'Delete'); ?>?</td>
   	</tr>
	<?php } ?>
    </table>
</div>

<?php echo $form->input('Poll.expires', array('label' => __d('forum', 'Expiration Date', true), 'type' => 'text', 'after' => ' '. __d('forum', 'How many days till expiration? Leave blank to last forever.', true), 'style' => 'width: 50px'));
} ?>

<?php echo $form->input('FirstPost.id', array('type' => 'hidden')); ?>
<?php echo $form->input('FirstPost.content', array('type' => 'textarea', 'rows' => 15, 'label' => __d('forum', 'Content', true))); ?>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $form->end(__d('forum', 'Update', true)); ?>