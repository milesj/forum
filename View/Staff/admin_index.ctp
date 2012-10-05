<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index')); ?>

<div class="controls float-right">
	<?php
	echo $this->Html->link(__d('forum', 'Add Staff'), array('action' => 'add_access'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Access Level'), array('action' => 'add_access_level'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Moderator'), array('action' => 'add_moderator'), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><?php echo __d('forum', 'Staff &amp; Moderators'); ?></h2>
</div>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Levels'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th style="width: 20%"><?php echo __d('forum', 'Title'); ?></th>
					<th style="width: 20%"><?php echo __d('forum', 'Level'); ?></th>
					<th style="width: 20%"><?php echo __d('forum', 'Is Admin'); ?></th>
					<th style="width: 20%"><?php echo __d('forum', 'Is Super Mod'); ?></th>
					<th style="width: 20%"><?php echo __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($levels as $level) { ?>

				<tr>
					<td class="align-center"><?php echo h($level['AccessLevel']['title']); ?></td>
					<td class="align-center"><?php echo $level['AccessLevel']['level']; ?></td>
					<td class="align-center"><?php echo $level['AccessLevel']['isAdmin'] ? __d('forum', 'Yes') : __d('forum', 'No'); ?></td>
					<td class="align-center"><?php echo $level['AccessLevel']['isSuper'] ? __d('forum', 'Yes') : __d('forum', 'No'); ?></td>
					<td class="align-center gray">
						<?php if ($level['AccessLevel']['id'] <= Access::ADMIN) { ?>
							<em><?php echo __d('forum', 'Restricted'); ?></em>
						<?php } else { ?>
							<?php echo $this->Html->link(__d('forum', 'Edit'), array('action' => 'edit_access_level', $level['AccessLevel']['id'])); ?> -
							<?php echo $this->Html->link(__d('forum', 'Delete'), array('action' => 'delete_access_level', $level['AccessLevel']['id']));
						} ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
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
					<th style="width: 25%"><?php echo __d('forum', 'Achieved On'); ?></th>
					<th style="width: 25%"><?php echo __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($staff as $user) { ?>

				<tr>
					<td><strong><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['User']['Profile']['id'], 'admin' => true)); ?></strong></td>
					<td class="align-center"><?php echo h($user['AccessLevel']['title']); ?></td>
					<td class="align-center"><?php echo $this->Time->nice($user['Access']['created'], $this->Common->timezone()); ?></td>
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
					<td><strong><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['User']['Profile']['id'], 'admin' => true)); ?></strong></td>
					<td class="align-center"><?php echo $this->Html->link($user['Forum']['title'], array('controller' => 'stations', 'action' => 'edit', $user['Forum']['id'], 'admin' => true)); ?></td>
					<td class="align-center"><?php echo $this->Time->nice($user['Moderator']['created'], $this->Common->timezone()); ?></td>
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
	echo $this->Html->link(__d('forum', 'Add Access Level'), array('action' => 'add_access_level'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Moderator'), array('action' => 'add_moderator'), array('class' => 'button')); ?>
</div>

