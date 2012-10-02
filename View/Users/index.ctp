<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index')); ?>

<?php echo $this->Form->create('Profile', array('url' => array('controller' => 'users', 'action' => 'proxy'))); ?>

<div class="filter">
	<?php echo $this->Form->input('username', array('div' => false, 'label' => __d('forum', 'Search Users (Username)') . ': ')); ?>
	<?php echo $this->Form->submit(__d('forum', 'Search'), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'User List'); ?></h2>
</div>

<?php echo $this->Form->end(); ?>

<div class="container" id="users">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
		<tr>
			<th><?php echo $this->Paginator->sort('User.' . $config['userMap']['username'], __d('forum', 'Username')); ?></th>
			<th><?php echo $this->Paginator->sort('Profile.created', __d('forum', 'Joined')); ?></th>
			<th><?php echo $this->Paginator->sort('Profile.lastLogin', __d('forum', 'Last Active')); ?></th>
			<th><?php echo $this->Paginator->sort('Profile.totalTopics', __d('forum', 'Topics')); ?></th>
			<th><?php echo $this->Paginator->sort('Profile.totalPosts', __d('forum', 'Posts')); ?></th>
		</tr>

		<?php if ($users) {
			foreach ($users as $counter => $profile) { ?>

			<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
				<td><?php echo $this->Html->link($profile['User'][$config['userMap']['username']], array('action' => 'profile', $profile['User']['id'])); ?></td>
				<td class="created"><?php echo $this->Time->nice($profile['Profile']['created'], $this->Common->timezone()); ?></td>
				<td class="created">
					<?php if (!empty($profile['Profile']['lastLogin'])) {
						echo $this->Time->timeAgoInWords($profile['Profile']['lastLogin'], array('userOffset' => $this->Common->timezone()));
					} else {
						echo '<em class="gray">' . __d('forum', 'Never') . '</em>';
					} ?>
				</td>
				<td class="stat"><?php echo number_format($profile['Profile']['totalTopics']); ?></td>
				<td class="stat"><?php echo number_format($profile['Profile']['totalPosts']); ?></td>
			</tr>

			<?php }
		} else { ?>

			<tr>
				<td colspan="5" class="empty"><?php echo __d('forum', 'There are no users to display.'); ?></td>
			</tr>

		<?php } ?>

		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>