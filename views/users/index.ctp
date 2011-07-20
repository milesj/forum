<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'index')); ?>

<?php echo $this->Form->create('Profile', array('url' => array('controller' => 'users', 'action' => 'proxy'))); ?>

<div class="filter">
	<?php echo $this->Form->input('username', array('div' => false, 'label' => __d('forum', 'Search Users (Username)', true) .': ')); ?>
	<?php echo $this->Form->submit(__d('forum', 'Search', true), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<div class="title">
	<h2><?php __d('forum', 'User List'); ?></h2>
</div>

<?php echo $this->Form->end(); ?>

<div class="container" id="users">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
		<tr>
			<th><?php echo $this->Paginator->sort(__d('forum', 'Username', true), 'User.'. $config['userMap']['username']); ?></th>
			<th><?php echo $this->Paginator->sort(__d('forum', 'Joined', true), 'Profile.created'); ?></th>
			<th><?php echo $this->Paginator->sort(__d('forum', 'Last Active', true), 'Profile.lastLogin'); ?></th>
			<th><?php echo $this->Paginator->sort(__d('forum', 'Topics', true), 'Profile.totalTopics'); ?></th>
			<th><?php echo $this->Paginator->sort(__d('forum', 'Posts', true), 'Profile.totalPosts'); ?></th>
		</tr>

		<?php if (!empty($users)) {
			foreach ($users as $counter => $user) { ?>

			<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
				<td><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('action' => 'profile', $user['User']['id'])); ?></td>
				<td class="created"><?php echo $this->Time->nice($user['Profile']['created'], $this->Common->timezone()); ?></td>
				<td class="created">
					<?php if (!empty($user['Profile']['lastLogin'])) {
						echo $this->Time->relativeTime($user['Profile']['lastLogin'], array('userOffset' => $this->Common->timezone()));
					} else {
						echo '<em class="gray">'. __d('forum', 'Never', true) .'</em>';
					} ?>
				</td>
				<td class="stat"><?php echo number_format($user['Profile']['totalTopics']); ?></td>
				<td class="stat"><?php echo number_format($user['Profile']['totalPosts']); ?></td>
			</tr>

			<?php }
		} else { ?>

			<tr>
				<td colspan="5" class="empty"><?php __d('forum', 'There are no users signed up on this forum.'); ?></td>
			</tr>

		<?php } ?>

		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>	