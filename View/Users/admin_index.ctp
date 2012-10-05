<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index')); ?>

<?php echo $this->Form->create('Profile'); ?>

<div class="filter">
    <?php echo __d('forum', 'Search Users'); ?>
	<?php echo $this->Form->input('username', array('div' => false, 'label' => '(' . __d('forum', 'Username') . '): ')); ?>
	<?php echo $this->Form->input('id', array('div' => false, 'label' => '(' . __d('forum', 'ID') . '): ', 'type' => 'text', 'class' => 'numeric')); ?>
	<?php echo $this->Form->submit(__d('forum', 'Search'), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'Manage Users'); ?></h2>
</div>

<?php echo $this->Form->end(); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('Profile.id', '#'); ?></th>
					<th><?php echo $this->Paginator->sort('User.' . $config['userMap']['username'], __d('forum', 'Username')); ?></th>
					<th><?php echo $this->Paginator->sort('User.' . $config['userMap']['status'], __d('forum', 'Status')); ?></th>
					<th><?php echo $this->Paginator->sort('User.' . $config['userMap']['email'], __d('forum', 'Email')); ?></th>
					<th><?php echo $this->Paginator->sort('Profile.created', __d('forum', 'Joined')); ?></th>
					<th><?php echo $this->Paginator->sort('Profile.lastLogin', __d('forum', 'Last Active')); ?></th>
					<th><?php echo $this->Paginator->sort('Profile.totalTopics', __d('forum', 'Topics')); ?></th>
					<th><?php echo $this->Paginator->sort('Profile.totalPosts', __d('forum', 'Posts')); ?></th>
					<th><?php echo __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if ($users) {
				foreach ($users as $counter => $user) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td class="icon"><?php echo $user['Profile']['id']; ?></td>
					<td><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('action' => 'edit', $user['Profile']['id'], 'admin' => true)); ?></td>
					<td><?php echo $this->Common->options('statusMap', $user['User'][$config['userMap']['status']]); ?></td>
					<td><?php echo $user['User'][$config['userMap']['email']]; ?></td>
					<td><?php echo $this->Time->nice($user['Profile']['created'], $this->Common->timezone()); ?></td>
					<td>
						<?php if (!empty($user['Profile']['lastLogin'])) {
							echo $this->Time->timeAgoInWords($user['Profile']['lastLogin'], array('userOffset' => $this->Common->timezone()));
						} else {
							echo '<em class="gray">' . __d('forum', 'Never') . '</em>';
						} ?>
					</td>
					<td class="stat"><?php echo number_format($user['Profile']['totalTopics']); ?></td>
					<td class="stat"><?php echo number_format($user['Profile']['totalPosts']); ?></td>
					<td class="align-center">
						<?php echo $this->Html->link(__d('forum', 'Edit'), array('action' => 'edit', $user['Profile']['id'], 'admin' => true)); ?>
					</td>
				</tr>

				<?php }
			} else { ?>

				<tr>
					<td colspan="8" class="empty"><?php echo __d('forum', 'There are no users signed up on this forum'); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>