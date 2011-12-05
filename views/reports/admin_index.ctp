<?php 

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Reported', true), array('controller' => 'reports', 'action' => 'index')); ?>

<div class="controls float-right">
	<?php 
	echo $this->Html->link(__d('forum', 'Topics', true), array('controller' => 'reports', 'action' => 'topics'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Posts', true), array('controller' => 'reports', 'action' => 'posts'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Users', true), array('controller' => 'reports', 'action' => 'users'), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><?php __d('forum', 'Reported Items'); ?></h2>
</div>

<?php echo $this->Form->create('Report'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><?php __d('forum', 'Type'); ?></th>
					<th><?php __d('forum', 'Item'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Reported By', true), 'Reporter.'. $config['userMap']['username']); ?></th>
					<th><?php __d('forum', 'Comment'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Reported On', true), 'Report.created'); ?></th>
				</tr>
			</thead>
			<tbody>

				<?php if (!empty($reports)) {
					foreach ($reports as $counter => $report) { ?>

					<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
						<td><?php echo $this->Html->link($this->Common->reportType($report['Report']['itemType']), array($report['Report']['itemType'])); ?></td>
						<td>	
							<?php if ($report['Report']['itemType'] == Report::TOPIC && !empty($report['Topic']['id'])) {
								echo $this->Html->link($report['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $report['Topic']['slug'], 'admin' => false));

							} else if ($report['Report']['itemType'] == Report::USER && !empty($report['User']['id'])) {
								echo $this->Html->link($report['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['User']['Profile']['id'], 'admin' => true));

							} else if ($report['Report']['itemType'] == Report::POST && !empty($report['Post']['id'])) {
								echo $report['Post']['content'];

							} else {
								echo '<em class="gray">('. __d('forum', 'Deleted', true) .')</em>';
							} ?>
						</td>
						<td><?php echo $this->Html->link($report['Reporter'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['Profile']['id'], 'admin' => true)); ?></td>
						<td><?php echo $report['Report']['comment']; ?></td>
						<td><?php echo $this->Time->nice($report['Report']['created'], $this->Common->timezone()); ?></td>
					</tr>
					<?php }
				} else { ?>

					<tr>
						<td colspan="5" class="empty"><?php __d('forum', 'There are no reported items to display.'); ?></td>
					</tr>
					
				<?php } ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>	

<?php echo $this->Form->end(); ?>