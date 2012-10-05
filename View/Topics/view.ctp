<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug'])); ?>

<div class="title">
	<h2>
		<?php if ($topic['Topic']['type'] > Topic::NORMAL) {
			echo '<span>' . $this->Common->options('topicTypes', $topic['Topic']['type']) . ':</span> ';
		} else if ($topic['Topic']['status'] == Topic::STATUS_CLOSED) {
			echo '<span>' . __d('forum', 'Closed') . ':</span> ';
		}

		echo h($topic['Topic']['title']); ?>
	</h2>
</div>

<?php echo $this->element('tiles/topic_controls', array(
	'topic' => $topic
));

if (!empty($topic['Poll']['id'])) { ?>

	<div id="poll" class="container">
		<div class="containerHeader">
			<h3><?php echo $topic['Topic']['title']; ?></h3>
		</div>

		<div class="containerContent">
			<?php echo $this->Form->create('Poll'); ?>

			<table class="table">
				<tbody>

				<?php if (!$topic['Poll']['hasVoted']) {
					foreach ($topic['Poll']['PollOption'] as $counter => $option) { ?>

					<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
						<td class="icon">
							<input type="radio" name="data[Poll][option]" value="<?php echo $option['id']; ?>"<?php if ($counter == 0) echo ' checked="checked"'; ?> />
						</td>
						<td colspan="2">
							<?php echo $option['option']; ?>
						</td>
					</tr>

					<?php } ?>

					<tr class="headRow">
						<td colspan="3" class="align-center">
							<?php if ($user) {
								if (!empty($topic['Poll']['expires']) && $topic['Poll']['expires'] <= date('Y-m-d H:i:s')) {
									__d('forum', 'Voting on this poll has been closed');
								} else {
									echo $this->Form->submit(__d('forum', 'Vote'), array('div' => false, 'class' => 'button'));
								}
							} else {
								__d('forum', 'Please login to vote!');
							} ?>
						</td>
					</tr>

				<?php } else {
					foreach ($topic['Poll']['PollOption'] as $counter => $option) { ?>

					<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
						<td class="dark align-right">
							<strong><?php echo $option['option']; ?></strong>
						</td>
						<td style="width: 50%">
							<div class="pollBar" style="width: <?php echo $option['percentage']; ?>%"></div>
						</td>
						<td>
							<?php echo sprintf(__d('forum', '%d votes'), number_format($option['vote_count'])); ?> (<?php echo $option['percentage']; ?>%)

							<?php if ($topic['Poll']['hasVoted'] == $option['id']) {
								echo '<em>(' . __d('forum', 'Your Vote') . ')</em>';
							} ?>
						</td>
					</tr>

				<?php } } ?>

				</tbody>
			</table>

			<?php echo $this->Form->end(); ?>
		</div>
	</div>

<?php } ?>

<div class="container" id="posts">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table posts">
			<tbody>
				<?php foreach ($posts as $post) { ?>

				<tr class="altRow" id="post-<?php echo $post['Post']['id']; ?>">
					<td class="align-right dark">
						<?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?>
					</td>
					<td class="align-right dark">
						<?php if ($user) {
							$links = array();

							if ($topic['Topic']['firstPost_id'] == $post['Post']['id']) {
								if ($this->Common->hasAccess(AccessLevel::SUPER, $topic['Forum']['id']) || ($topic['Topic']['status'] && $user['User']['id'] == $post['Post']['user_id'])) {
									$links[] = $this->Html->link(__d('forum', 'Edit Topic'), array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['slug']));
								}

								if ($this->Common->hasAccess(AccessLevel::SUPER, $topic['Forum']['id'])) {
									$links[] = $this->Html->link(__d('forum', 'Delete Topic'), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('confirm' => __d('forum', 'Are you sure you want to delete?')));
								}

								$links[] = $this->Html->link(__d('forum', 'Report Topic'), array('controller' => 'topics', 'action' => 'report', $topic['Topic']['slug']));
							} else {
								if ($user['User']['id'] == $post['Post']['user_id']) {
									$links[] = $this->Html->link(__d('forum', 'Edit Post'), array('controller' => 'posts', 'action' => 'edit', $post['Post']['id']));
									$links[] = $this->Html->link(__d('forum', 'Delete Post'), array('controller' => 'posts', 'action' => 'delete', $post['Post']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?')));
								}

								$links[] = $this->Html->link(__d('forum', 'Report Post'), array('controller' => 'posts', 'action' => 'report', $post['Post']['id']));
							}

							if ($topic['Topic']['status'] && $this->Common->hasAccess($topic['Forum']['accessReply'])) {
								$links[] = $this->Html->link(__d('forum', 'Quote'), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'], $post['Post']['id']));
							}

							if ($links) {
								echo implode(' - ', $links);
							}
						} ?>
					</td>
				</tr>
				<tr>
					<td valign="top" style="width: 25%">
						<h4 class="username"><?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></h4>

						<?php if (!empty($post['User']['Access'])) { ?>
							<strong><?php echo $this->Common->highestAccessLevel($post['User']['Access']); ?></strong><br />
						<?php } ?>

						<?php if ($settings['enable_gravatar']) { ?>
							<div class="avatar">
								<?php echo $this->Common->gravatar($post['User'][$config['userMap']['email']], array('size' => 100)); ?>
							</div>
						<?php } ?>

						<strong><?php echo __d('forum', 'Total Topics'); ?>:</strong> <?php echo number_format($post['User']['Profile']['totalTopics']); ?><br />
						<strong><?php echo __d('forum', 'Total Posts'); ?>:</strong> <?php echo number_format($post['User']['Profile']['totalPosts']); ?>
					</td>
					<td valign="top">
						<div class="post">
							<?php echo $post['Post']['contentHtml']; ?>
						</div>

						<?php if (!empty($post['User']['Profile']['signatureHtml'])) { ?>
							<div class="signature">
								<?php echo $post['User']['Profile']['signatureHtml']; ?>
							</div>
						<?php } ?>
					</td>
				</tr>

				<?php } ?>
			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>

<?php echo $this->element('tiles/topic_controls', array(
	'topic' => $topic
));

if ($user && $topic['Topic']['status'] && $settings['enable_quick_reply'] && $this->Common->hasAccess($topic['Forum']['accessReply'])) { ?>

	<div id="quickReply" class="container">
		<div class="containerHeader">
			<h3><?php echo __d('forum', 'Quick Reply'); ?></h3>
		</div>

		<div class="containerContent">
			<?php echo $this->Form->create('Post', array(
				'url' => array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'])
			)); ?>

			<table class="table">
				<tbody>
					<tr>
						<td style="width: 25%">
							<strong><?php echo $this->Form->label('content', __d('forum', 'Message') . ':'); ?></strong><br /><br />

							<?php echo $this->Html->link(__d('forum', 'Advanced Reply'), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'])); ?><br />
							<?php echo __d('forum', 'BBCode Enabled'); ?>
						</td>
						<td>
							<?php echo $this->Form->input('content', array(
								'after' => '<span class="inputText" style="margin-left: 0; padding: 0;">[b], [u], [i], [s], [img], [url], [email], [color], [size], [left], [center], [right], [justify], [list], [olist], [li], [quote], [code]</span>',
								'type' => 'textarea',
								'rows' => 5,
								'style' => 'width: 99%',
								'div' => false,
								'error' => false,
								'label' => false
							)); ?>

							<?php echo $this->element('markitup', array('textarea' => 'PostContent')); ?>
						</td>
					</tr>
					<tr class="headRow">
						<td colspan="2" class="align-center">
							<?php echo $this->Form->submit(__d('forum', 'Post Reply'), array('class' => 'button', 'div' => false)); ?>
						</td>
					</tr>
				</tbody>
			</table>

			<?php echo $this->Form->end(); ?>
		</div>
	</div>

<?php } ?>
