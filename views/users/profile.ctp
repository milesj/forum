<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'index'));
$this->Html->addCrumb($profile['User'][$config['userMap']['username']], $this->here); ?>

<div class="title">
	<?php echo $this->Html->link(__d('forum', 'Report User', true), array('action' => 'report', $profile['User']['id']), array('class' => 'button float-right')); ?>
	<h2><?php echo $profile['User'][$config['userMap']['username']]; ?></h2>
</div>

<?php if (!empty($profile['Profile']['signatureHtml'])) { ?>
	<p><?php echo $profile['Profile']['signatureHtml']; ?></p>
<?php } ?>

<div class="container">
	<table class="table">
		<tbody>
			<tr>
				<?php if ($settings['enable_gravatar']) { ?>
					<td rowspan="2" style="width: 80px;">
						<?php echo $this->Gravatar->image($profile['User'][$config['userMap']['email']]); ?>
					</td>
				<?php } ?>

				<td><strong><?php __d('forum', 'Joined'); ?>:</strong></td>
				<td><?php echo $this->Time->nice($profile['User']['created'], $this->Common->timezone()); ?></td>

				<td><strong><?php __d('forum', 'Total Topics'); ?>:</strong></td>

				<td><?php echo number_format($profile['Profile']['totalTopics']); ?></td>
				<td><strong><?php __d('forum', 'Roles'); ?>:</strong></td>

				<td>
					<?php if (!empty($profile['User']['Access'])) { 
						$roles = array();
						foreach ($profile['User']['Access'] as $access) {
							$roles[] = $access['AccessLevel']['title'];
						}
						echo implode(', ', $roles);
					} else {
						echo '<em>'. __d('forum', 'N/A', true) .'</em>';
					} ?>
				</td>
			</tr>
			<tr>
				<td><strong><?php __d('forum', 'Last Login'); ?>:</strong></td>
				<td>
					<?php if (!empty($profile['Profile']['lastLogin'])) {
						echo $this->Time->relativeTime($profile['Profile']['lastLogin'], array('userOffset' => $this->Common->timezone()));
					} else {
						echo '<em>'. __d('forum', 'Never', true) .'</em>';
					} ?>
				</td>

				<td><strong><?php __d('forum', 'Total Posts'); ?>:</strong></td>
				<td><?php echo number_format($profile['Profile']['totalPosts']); ?></td>

				<td><strong><?php __d('forum', 'Moderates'); ?>:</strong></td>
				<td>
					<?php if (!empty($profile['User']['Moderator'])) { 
						$mods = array();
						foreach ($profile['User']['Moderator'] as $mod) {
							$mods[] = $this->Html->link($mod['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $mod['Forum']['slug']));
						}
						echo implode(', ', $mods);
					} else {
						echo '<em>'. __d('forum', 'N/A', true) .'</em>';
					} ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
	
<?php if (!empty($topics)) { ?>
	
<div class="container">
	<div class="containerHeader">
		<h3><?php __d('forum', 'Latest Topics'); ?></h3>
	</div>
	
	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php __d('forum', 'Topic'); ?></th>
					<th><?php __d('forum', 'Created'); ?></th>
					<th><?php __d('forum', 'Posts'); ?></th>
					<th><?php __d('forum', 'Views'); ?></th>
					<th><?php __d('forum', 'Last Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($topics as $counter => $topic) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td><?php echo $this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?></td>
					<td class="created"><?php echo $this->Time->niceShort($topic['Topic']['created'], $this->Common->timezone()); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['post_count']); ?></td>
					<td class="stat"><?php echo number_format($topic['Topic']['view_count']); ?></td>
					<td class="activity"><?php echo $this->Time->relativeTime($topic['LastPost']['created'], array('userOffset' => $this->Common->timezone())); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>   
	
<?php }

if (!empty($posts)) { ?>
	
<div class="container">
	<div class="containerHeader">
		<h3><?php __d('forum', 'Latest Posts'); ?></h3>
	</div>
	
	<div class="containerContent">
		<table class="table">
			<thead>
				<tr>
					<th><?php __d('forum', 'Topic'); ?></th>
					<th><?php __d('forum', 'Author'); ?></th>
					<th><?php __d('forum', 'Posted On'); ?></th>
				</tr>
			</thead>
			<tbody>
				
			<?php foreach($posts as $post) { ?>

				<tr class="altRow">
					<td><strong><?php echo $this->Html->link($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug'])); ?></strong></td>
					<td><?php echo $this->Html->link($post['Topic']['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['Topic']['User']['id'])); ?></td>
					<td class="ar"><?php echo $this->Time->relativeTime($post['Post']['created'], array('userOffset' => $this->Common->timezone())); ?></td>
				</tr>
				<tr>
					<td colspan="3"><?php echo $post['Post']['contentHtml']; ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>    
</div>
	
<?php } ?>