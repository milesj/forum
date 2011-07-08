<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="title">
	<h2><?php __d('forum', 'Moderate'); ?>: <?php echo $forum['Forum']['title']; ?></h2>
</div>

<?php echo $this->Form->create('Topic', array('url' => $this->here)); ?>

<div class="controls">
	<?php echo $this->Html->link(__d('forum', 'Return to Forum', true), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']), array('class' => 'button')); ?>
</div>

<div class="container" id="topics">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="forum.toggleCheckboxes(this);" /></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Status', true), 'Topic.status'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Author', true), 'User.'. $config['userMap']['username']); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if (!empty($topics)) {
				foreach ($topics as $counter => $topic) {
					echo $this->element('tiles/topic_row', array(
						'topic' => $topic,
						'counter' => $counter,
						'columns' => array('status')
					));
				}
			} else { ?>

				<tr>
					<td colspan="7" class="empty"><?php __d('forum', 'There are no topics within this forum category.'); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>

<div class="controls">
	<?php echo $this->Html->link(__d('forum', 'Return to Forum', true), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']), array('class' => 'button')); ?>
</div>

<div class="moderate">
	<?php 
	echo $this->Form->input('action', array(
		'options' => array(
			'move' => __d('forum', 'Move Topic(s)', true),
			'open' => __d('forum', 'Open Topic(s)', true),
			'close' => __d('forum', 'Close Topic(s)', true),
			'delete' => __d('forum', 'Delete Topic(s)', true)
		),
		'div' => false, 
		'label' => __d('forum', 'Perform Action', true) .': '
	));
	
	echo $this->Form->input('move_id', array('options' => $forums, 'div' => false, 'label' => __d('forum', 'Move To', true) .': ', 'escape' => false));
	echo $this->Form->submit(__d('forum', 'Process', true), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<?php echo $this->Form->end(); ?>
