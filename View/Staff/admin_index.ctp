<?php

$this->Breadcrumb->add(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Breadcrumb->add(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index')); ?>

<div class="controls float-right">
	<?php
	echo $this->Html->link(__d('forum', 'Add Staff'), array('action' => 'add_access'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Moderator'), array('action' => 'add_moderator'), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'Staff &amp; Moderators'); ?></h2>
</div>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Staff'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th style="width: 25%"><?php echo __d('forum', 'User'); ?></th>
					<th style="width: 25%"><?php echo __d('forum', 'Access Level'); ?></th>
					<th style="width: 25%"><?php echo __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($staff as $user) { ?>

				<tr>
					<td><strong><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['User']['ForumProfile']['id'], 'admin' => true)); ?></strong></td>
					<td class="align-center"><?php echo $this->Forum->options('accessGroups', $user['Group']['id']) ?></td>
					<td class="align-center gray">
						<?php echo $this->Html->link(__d('forum', 'Edit'), array('action' => 'edit_access', $user['Access']['id'])); ?> -
						<?php echo $this->Html->link(__d('forum', 'Delete'), array('action' => 'delete_access', $user['Access']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?'))); ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Moderators'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th style="width: 25%"><?php echo __d('forum', 'User'); ?></th>
					<th style="width: 25%"><?php echo __d('forum', 'Moderates'); ?></th>
					<th style="width: 25%"><?php echo __d('forum', 'Achieved On'); ?></th>
					<th style="width: 25%"><?php echo __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if ($mods) {
				foreach ($mods as $user) { ?>

				<tr >
					<td>
						<strong>
						<?php if (!empty($user['User']['ForumProfile'])) {
							echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['User']['ForumProfile']['id'], 'admin' => true));
						} else {
							echo $user['User'][$config['userMap']['username']];
						} ?>
						</strong>
					</td>
					<td class="align-center"><?php echo $this->Html->link($user['Forum']['title'], array('controller' => 'stations', 'action' => 'edit', $user['Forum']['id'], 'admin' => true)); ?></td>
					<td class="align-center"><?php echo $this->Time->nice($user['Moderator']['created'], $this->Forum->timezone()); ?></td>
					<td class="align-center gray">
						<?php echo $this->Html->link(__d('forum', 'Edit'), array('action' => 'edit_moderator', $user['Moderator']['id'])); ?> -
						<?php echo $this->Html->link(__d('forum', 'Delete'), array('action' => 'delete_moderator', $user['Moderator']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?'))); ?>
					</td>
				</tr>

				<?php }
			} else { ?>

				<tr>
					<td colspan="4" class="empty"><?php echo __d('forum', 'There are no assigned moderators.'); ?> <?php echo $this->Html->link(__d('forum', 'Add Moderator'), array('action' => 'add_moderator')); ?>.</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<div class="controls">
	<?php
	echo $this->Html->link(__d('forum', 'Add Staff'), array('action' => 'add_access'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Moderator'), array('action' => 'add_moderator'), array('class' => 'button')); ?>
</div>

