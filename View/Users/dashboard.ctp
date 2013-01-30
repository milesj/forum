<?php

$this->Breadcrumb->add(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index'));
$this->Breadcrumb->add(__d('forum', 'Dashboard'), array('action' => 'dashboard')); ?>

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
						<?php echo $this->Html->link($topic['Topic']['User'][$config['userMap']['username']], $this->Forum->profileUrl($topic['Topic']['User'])); ?>
					</td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Subscription']['created'], $this->Forum->timezone()); ?></td>
					<td class="activity">
						<?php echo $this->Time->timeAgoInWords($topic['Topic']['LastPost']['created'], array('userOffset' => $this->Forum->timezone()));

						if (!empty($topic['Topic']['LastUser'])) { ?>
							<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['Topic']['LastUser'][$config['userMap']['username']], $this->Forum->profileUrl($topic['Topic']['LastUser'])); ?></span>
						<?php } ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php } ?>

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

			<?php if ($activity) {
				foreach ($activity as $counter => $topic) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
					<td class="author">
						<?php echo $this->Html->link($topic['Topic']['User'][$config['userMap']['username']], $this->Forum->profileUrl($topic['Topic']['User'])); ?>
					</td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Forum->timezone()); ?></td>
					<td class="activity">
						<?php echo $this->Time->timeAgoInWords($topic['Topic']['LastPost']['created'], array('userOffset' => $this->Forum->timezone()));

						if (!empty($topic['Topic']['LastUser'])) { ?>
							<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['Topic']['LastUser'][$config['userMap']['username']], $this->Forum->profileUrl($topic['Topic']['LastUser'])); ?></span>
						<?php } ?>
					</td>
				</tr>

			<?php }
			} else { ?>

				<tr>
					<td colspan="6" class="empty">
						<?php echo __d('forum', 'No latest activity to display'); ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php if ($topics) { ?>

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
					<td class="created"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Forum->timezone()); ?></td>
					<td class="activity">
						<?php echo $this->Time->timeAgoInWords($topic['LastPost']['created'], array('userOffset' => $this->Forum->timezone()));

						if (!empty($topic['LastUser'])) { ?>
							<span class="gray"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($topic['LastUser'][$config['userMap']['username']], $this->Forum->profileUrl($topic['Topic']['LastUser'])); ?></span>
						<?php } ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php } ?>