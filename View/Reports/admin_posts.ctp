<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Reported'), array('controller' => 'reports', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Posts'), array('controller' => 'reports', 'action' => 'posts')); ?>

<div class="controls float-right">
	<?php
	echo $this->Html->link(__d('forum', 'Topics'), array('controller' => 'reports', 'action' => 'topics'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Posts'), array('controller' => 'reports', 'action' => 'posts'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Users'), array('controller' => 'reports', 'action' => 'users'), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'Reported Posts'); ?></h2>
</div>

<?php echo $this->Form->create('Report'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th><?php echo __d('forum', 'Topic'); ?></th>
					<th><?php echo __d('forum', 'Post'); ?></th>
					<th><?php echo $this->Paginator->sort('Reporter.'. $config['userMap']['username'], __d('forum', 'Reported By')); ?></th>
					<th><?php echo __d('forum', 'Comment'); ?></th>
					<th><?php echo $this->Paginator->sort('Report.created', __d('forum', 'Reported On')); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if (!empty($reports)) {
				foreach ($reports as $counter => $report) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td class="icon"><input type="checkbox" name="data[Report][items][]" value="<?php echo $report['Report']['id']; ?>:<?php echo $report['Post']['id']; ?>" /></td>
					<td>
						<?php if (!empty($report['Post']['Topic']['id'])) {
							echo $this->Html->link($report['Post']['Topic']['title'], array('controller' => 'forum', 'action' => 'jump', $report['Post']['topic_id'], $report['Post']['id'], 'admin' => false));
						} else {
							echo '<em class="gray">('. __d('forum', 'Deleted') .')</em>';
						} ?>
					</td>
					<td>
						<?php if (!empty($report['Post']['id'])) {
							echo $report['Post']['content'];
						} else {
							echo '<em class="gray">('. __d('forum', 'Deleted') .')</em>';
						} ?>
					</td>
					<td><?php echo $this->Html->link($report['Reporter'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['Profile']['id'], 'admin' => true)); ?></td>
					<td><?php echo $report['Report']['comment']; ?></td>
					<td><?php echo $this->Time->nice($report['Report']['created'], $this->Common->timezone()); ?></td>
				</tr>

				<?php }
			} else { ?>

				<tr>
					<td colspan="5" class="empty"><?php echo __d('forum', 'There are no reported posts.'); ?></td>
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
			'delete' => __d('forum', 'Delete Post(s)'),
			'remove' => __d('forum', 'Remove Report Only')
		),
		'div' => false,
		'label' => __d('forum', 'Perform Action') .': '
	));

	echo $this->Form->submit(__d('forum', 'Process'), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<?php echo $this->Form->end(); ?>