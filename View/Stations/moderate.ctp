<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="controls float-right">
	<?php echo $this->Html->link(__d('forum', 'Return to Forum'), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'Moderate'); ?>: <?php echo $forum['Forum']['title']; ?></h2>
</div>

<?php echo $this->Form->create('Topic', array(
	'url' => array('controller' => 'stations', $forum['Forum']['slug'])
)); ?>

<div class="container" id="topics">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="Forum.toggleCheckboxes(this);" /></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Topic'), 'Topic.title'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Status'), 'Topic.status'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Author'), 'User.'. $config['userMap']['username']); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Created'), 'Topic.created'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Posts'), 'Topic.post_count'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Views'), 'Topic.view_count'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Activity'), 'LastPost.created'); ?></th>
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
					<td colspan="8" class="empty"><?php echo __d('forum', 'There are no topics within this forum.'); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>

<div class="moderate">
	<?php 
	echo $this->Form->input('action', array(
		'options' => array(
			'open' => __d('forum', 'Open Topic(s)'),
			'close' => __d('forum', 'Close Topic(s)'),
			'move' => __d('forum', 'Move Topic(s)'),
			'delete' => __d('forum', 'Delete Topic(s)')
		),
		'div' => false, 
		'label' => __d('forum', 'Perform Action') .': '
	));
	
	echo $this->Form->input('move_id', array('options' => $forums, 'div' => false, 'label' => __d('forum', 'Move To') .': ', 'escape' => false));
	echo $this->Form->submit(__d('forum', 'Process'), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<?php echo $this->Form->end(); ?>
