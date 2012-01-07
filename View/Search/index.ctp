<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Search'); ?></h2>
</div>

<?php echo $this->Form->create('Topic', array('url' => array('controller' => 'search', 'action' => 'proxy'))); ?>

<div class="container searchForm" id="search">
	<div class="containerContent">
		<table style="width: 100%">
			<tbody>
				<tr>
					<td class="align-right"><?php echo $this->Form->label('keywords', __d('forum', 'Keywords') .':'); ?></td>
					<td><?php echo $this->Form->input('keywords', array('div' => false, 'label' => false, 'style' => 'width: 300px')); ?></td>

					<td class="align-right"><?php echo $this->Form->label('forum_id', __d('forum', 'Within Forum Category') .':'); ?></td>
					<td><?php echo $this->Form->input('forum_id', array('div' => false, 'label' => false, 'options' => $forums, 'escape' => false, 'empty' => true)); ?></td>

					<td class="align-right"><?php echo $this->Form->label('orderBy', __d('forum', 'Order By') .':'); ?></td>
					<td><?php echo $this->Form->input('orderBy', array('div' => false, 'label' => false, 'options' => $orderBy)); ?></td>

					<td class="align-right"><?php echo $this->Form->label('byUser', __d('forum', 'By User (Username)') .':'); ?></td>
					<td><?php echo $this->Form->input('byUser', array('div' => false, 'label' => false, 'style' => 'width: 150px')); ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php 
echo $this->Form->submit(__d('forum', 'Search Topics'), array('class' => 'button'));
echo $this->Form->end(); 

if ($searching) { ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th colspan="2"><?php echo $this->Paginator->sort(__d('forum', 'Topic'), 'Topic.title'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Forum'), 'Topic.forum_id'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Author'), 'User.'. $config['userMap']['username']); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Created'), 'Topic.created'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Posts'), 'Topic.post_count'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Views'), 'Topic.view_count'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Activity'), 'LastPost.created'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if (empty($topics)) { ?>

				<tr>
					<td colspan="8" class="empty"><?php echo __d('forum', 'No results were found, please refine your search criteria.'); ?></td>
				</tr>

			<?php } else {
				foreach ($topics as $counter => $topic) {
					echo $this->element('tiles/topic_row', array(
						'topic' => $topic,
						'counter' => $counter,
						'columns' => array('forum')
					));
				} 
			} ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>

<?php } ?>
