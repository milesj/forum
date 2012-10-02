<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Dashboard'), $this->here); ?>

<div class="title">
	<?php echo $this->Html->link(__d('forum', 'Edit Profile'), array('controller' => 'users', 'action' => 'edit', 'admin' => false), array('class' => 'button float-right')); ?>
	<h2><?php echo __d('forum', 'Dashboard'); ?></h2>
</div>

<?php if ($subscriptions) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Your Topic Subscriptions'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Topic'); ?></th>
					<th><?php echo __d('forum', 'Author'); ?></th>
					<th><?php echo __d('forum', 'Posts'); ?></th>
					<th><?php echo __d('forum', 'Views'); ?></th>
					<th><?php echo __d('forum', 'Subscribed On'); ?></th>
					<th><?php echo __d('forum', 'Last Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($subscriptions as $counter => $topic) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
					<td class="author">
						<?php echo $this->Html->link($topic['Topic']['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['User']['id'])); ?>
					</td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Subscription']['created'], $this->Common->timezone()); ?></td>
					<td class="activity">
						<?php echo $this->Time->timeAgoInWords($topic['Topic']['LastPost']['created'], array('userOffset' => $this->Common->timezone()));

						if (!empty($topic['Topic']['LastUser'])) { ?>
							<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['Topic']['LastUser'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
						<?php } ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php }

if ($activity) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Your Latest Activity'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Topic'); ?></th>
					<th><?php echo __d('forum', 'Author'); ?></th>
					<th><?php echo __d('forum', 'Posts'); ?></th>
					<th><?php echo __d('forum', 'Views'); ?></th>
					<th><?php echo __d('forum', 'Created'); ?></th>
					<th><?php echo __d('forum', 'Last Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($activity as $counter => $topic) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
					<td class="author">
						<?php echo $this->Html->link($topic['Topic']['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['User']['id'])); ?>
					</td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Common->timezone()); ?></td>
					<td class="activity">
						<?php echo $this->Time->timeAgoInWords($topic['Topic']['LastPost']['created'], array('userOffset' => $this->Common->timezone()));

						if (!empty($topic['Topic']['LastUser'])) { ?>
							<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['Topic']['LastUser'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
						<?php } ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php }

if ($topics) { ?>

<div class="container">
	<div class="containerHeader">
		<h3><?php echo __d('forum', 'Your Latest Topics'); ?></h3>
	</div>

	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __d('forum', 'Topic'); ?></th>
					<th><?php echo __d('forum', 'Posts'); ?></th>
					<th><?php echo __d('forum', 'Views'); ?></th>
					<th><?php echo __d('forum', 'Created'); ?></th>
					<th><?php echo __d('forum', 'Last Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($topics as $counter => $topic) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Common->timezone()); ?></td>
					<td class="activity">
						<?php echo $this->Time->timeAgoInWords($topic['LastPost']['created'], array('userOffset' => $this->Common->timezone()));

						if (!empty($topic['LastUser'])) { ?>
							<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['LastUser'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $topic['Topic']['lastUser_id'])); ?></span>
						<?php } ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php } ?>