<?php 

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'index')); ?>

<?php echo $this->Form->create('Profile', array(
	'url' => array('controller' => 'users')
)); ?>

<div class="filter">
    <?php __d('forum', 'Search Users'); ?>
	<?php echo $this->Form->input('username', array('div' => false, 'label' => '('. __d('forum', 'Username', true) .'): ')); ?>
	<?php echo $this->Form->input('id', array('div' => false, 'label' => '('. __d('forum', 'ID', true) .'): ', 'type' => 'text', 'class' => 'numeric')); ?>
	<?php echo $this->Form->submit(__d('forum', 'Search', true), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<div class="title">
	<h2><?php __d('forum', 'Manage Users'); ?></h2>
</div>

<?php echo $this->Form->end(); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><?php echo $this->Paginator->sort('#', 'Profile.id'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Username', true), 'User.'. $config['userMap']['username']); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Email', true), 'User.' . $config['userMap']['email']); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Joined', true), 'Profile.created'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Last Active', true), 'Profile.lastLogin'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Topics', true), 'Profile.totalTopics'); ?></th>
					<th><?php echo $this->Paginator->sort(__d('forum', 'Posts', true), 'Profile.totalPosts'); ?></th>
					<th><?php __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if (!empty($users)) {
				foreach ($users as $counter => $user) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td class="icon"><?php echo $user['Profile']['id']; ?></td>
					<td><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('action' => 'edit', $user['Profile']['id'], 'admin' => true)); ?></td>
					<td><?php echo $user['User']['email']; ?></td>
					<td><?php echo $this->Time->nice($user['Profile']['created'], $this->Common->timezone()); ?></td>
					<td>
						<?php if (!empty($user['Profile']['lastLogin'])) {
							echo $this->Time->relativeTime($user['Profile']['lastLogin'], array('userOffset' => $this->Common->timezone()));
						} else {
							echo '<em class="gray">'. __d('forum', 'Never', true) .'</em>';
						} ?>
					</td>
					<td class="stat"><?php echo number_format($user['Profile']['totalTopics']); ?></td>
					<td class="stat"><?php echo number_format($user['Profile']['totalPosts']); ?></td>
					<td class="align-center">
						<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit', $user['Profile']['id'], 'admin' => true)); ?>
					</td>
				</tr>

				<?php }
			} else { ?>

				<tr>
					<td colspan="8" class="empty"><?php __d('forum', 'There are no users signed up on this forum'); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>	