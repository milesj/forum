<?php 

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Staff', true), array('controller' => 'staff', 'action' => 'index')); ?>

<div class="controls float-right">
	<?php 
	echo $this->Html->link(__d('forum', 'Add Staff', true), array('action' => 'add_access'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Access Level', true), array('action' => 'add_access_level'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Moderator', true), array('action' => 'add_moderator'), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><?php __d('forum', 'Staff &amp; Moderators'); ?></h2>
</div>

<div class="container">
	<div class="containerHeader">
		<h3><?php __d('forum', 'Levels'); ?></h3>
	</div>
	
	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th style="width: 33%"><?php __d('forum', 'Title'); ?></th>
					<th style="width: 33%"><?php __d('forum', 'Level'); ?></th>
					<th style="width: 33%"><?php __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($levels as $level) { ?>

				<tr>
					<td class="align-center"><?php echo $level['AccessLevel']['title']; ?></td>
					<td class="align-center"><?php echo $level['AccessLevel']['level']; ?></td>
					<td class="align-center gray">
						<?php if ($level['AccessLevel']['id'] <= 4) { ?>
							<em><?php __d('forum', 'Restricted'); ?></em>
						<?php } else { ?>
							<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit_access_level', $level['AccessLevel']['id'])); ?> -
							<?php echo $this->Html->link(__d('forum', 'Delete', true), array('action' => 'delete_access_level', $level['AccessLevel']['id']));
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
		<h3><?php __d('forum', 'Staff'); ?></h3>
	</div>
	
	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th style="width: 25%"><?php __d('forum', 'User'); ?></th>
					<th style="width: 25%"><?php __d('forum', 'Access Level'); ?></th>
					<th style="width: 25%"><?php __d('forum', 'Achieved On'); ?></th>
					<th style="width: 25%"><?php __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($staff as $user) { ?>

				<tr>
					<td><strong><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['User']['id'], 'admin' => true)); ?></strong></td>
					<td class="align-center"><?php echo $user['AccessLevel']['title']; ?></td>
					<td class="align-center"><?php echo $this->Time->nice($user['Access']['created'], $this->Common->timezone()); ?></td>
					<td class="align-center gray">
						<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit_access', $user['Access']['id'])); ?> -
						<?php echo $this->Html->link(__d('forum', 'Delete', true), array('action' => 'delete_access', $user['Access']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true))); ?>
					</td>
				</tr>

			<?php } ?>
				
			</tbody>
		</table>
	</div>
</div>

<div class="container">
	<div class="containerHeader">
		<h3><?php __d('forum', 'Moderators'); ?></h3>
	</div>
	
	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th style="width: 25%"><?php __d('forum', 'User'); ?></th>
					<th style="width: 25%"><?php __d('forum', 'Moderates'); ?></th>
					<th style="width: 25%"><?php __d('forum', 'Achieved On'); ?></th>
					<th style="width: 25%"><?php __d('forum', 'Options'); ?></th>
				</tr>
			</thead>
			<tbody>
    
			<?php if (!empty($mods)) {
				foreach ($mods as $user) { ?>

				<tr >
					<td><strong><?php echo $this->Html->link($user['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'edit', $user['Moderator']['id'], 'admin' => true)); ?></strong></td>
					<td class="align-center"><?php echo $this->Html->link($user['Forum']['title'], array('controller' => 'stations', 'action' => 'edit', $user['Forum']['id'], 'admin' => true)); ?></td>
					<td class="align-center"><?php echo $this->Time->nice($user['Moderator']['created'], $this->Common->timezone()); ?></td>
					<td class="align-center gray">
						<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit_moderator', $user['Moderator']['id'])); ?> -
						<?php echo $this->Html->link(__d('forum', 'Delete', true), array('action' => 'delete_moderator', $user['Moderator']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true))); ?>
					</td>
				</tr>

				<?php }
			} else { ?>

				<tr>
					<td colspan="4" class="empty"><?php __d('forum', 'There are no assigned moderators.'); ?> <?php echo $this->Html->link(__d('forum', 'Add Moderator', true), array('action' => 'add_moderator')); ?>.</td>
				</tr>

			<?php } ?>
    
			</tbody>
		</table>
	</div>
</div>

<div class="controls">
	<?php 
	echo $this->Html->link(__d('forum', 'Add Staff', true), array('action' => 'add_access'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Access Level', true), array('action' => 'add_access_level'), array('class' => 'button'));
	echo $this->Html->link(__d('forum', 'Add Moderator', true), array('action' => 'add_moderator'), array('class' => 'button')); ?>
</div>

