<?php

$this->OpenGraph->description($this->Text->truncate($this->Decoda->strip($topic['FirstPost']['content']), 150));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));

$canReply = ($user && $topic['Topic']['status'] && $this->Forum->hasAccess('Forum.Post', 'create', $topic['Forum']['accessReply'])); ?>

<div class="title">
	<h2>
		<?php if ($topic['Topic']['type'] > Topic::NORMAL) {
			echo '<span>' . $this->Utility->enum('Forum.Topic', 'type', $topic['Topic']['type']) . ':</span> ';

		} else if ($topic['Topic']['status'] == Topic::CLOSED) {
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
						<?php if ($user) { ?>
							<td class="icon">
								<input type="radio" name="data[Poll][option]" value="<?php echo $option['id']; ?>"<?php if ($counter == 0) echo ' checked="checked"'; ?>>
							</td>
						<?php } ?>
						<td colspan="2">
							<?php echo $option['option']; ?>
						</td>
					</tr>

					<?php } ?>

					<tr class="headRow">
						<td colspan="3" class="align-center">
							<?php if ($user) {
								if (!empty($topic['Poll']['expires']) && $topic['Poll']['expires'] <= date('Y-m-d H:i:s')) {
									echo __d('forum', 'Voting on this poll has been closed');
								} else {
									echo $this->Form->submit(__d('forum', 'Vote'), array('div' => false, 'class' => 'button'));
								}
							} else {
								echo __d('forum', 'Please login to vote!');
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
							<?php echo sprintf(__d('forum', '%d votes'), number_format($option['poll_vote_count'])); ?> (<?php echo $option['percentage']; ?>%)

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
				<?php foreach ($posts as $post) {
					$post_id = $post['Post']['id'];
					$hasRated = in_array($post_id, $ratings);
					$isBuried = ($post['Post']['score'] <= $settings['ratingBuryThreshold']); ?>

				<tr class="altRow <?php if ($isBuried) echo 'is-buried'; ?>" id="post-<?php echo $post_id; ?>">
					<td class="align-right dark">
						<?php echo $this->Time->niceShort($post['Post']['created'], $this->Forum->timezone()); ?>
					</td>
					<td class="dark">
						<?php if ($user) { ?>
							<span class="float-right">
								<?php
								$links = array();
								$isMod = $this->Forum->isMod($topic['Forum']['id']);

								if ($topic['Topic']['firstPost_id'] == $post_id) {
									if ($isMod || ($topic['Topic']['status'] && $user['id'] == $post['Post']['user_id'])) {
										$links[] = $this->Html->link(__d('forum', 'Edit Topic'), array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['slug'], (!empty($topic['Poll']['id']) ? 'poll' : '')));
									}

									if ($isMod) {
										$links[] = $this->Html->link(__d('forum', 'Delete Topic'), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('confirm' => __d('forum', 'Are you sure you want to delete?')));
									}

									$links[] = $this->Html->link(__d('forum', 'Report Topic'), array('controller' => 'topics', 'action' => 'report', $topic['Topic']['slug']));
								} else {
									if ($isMod || ($topic['Topic']['status'] && $user['id'] == $post['Post']['user_id'])) {
										$links[] = $this->Html->link(__d('forum', 'Edit Post'), array('controller' => 'posts', 'action' => 'edit', $post_id));
										$links[] = $this->Html->link(__d('forum', 'Delete Post'), array('controller' => 'posts', 'action' => 'delete', $post_id), array('confirm' => __d('forum', 'Are you sure you want to delete?')));
									}

									$links[] = $this->Html->link(__d('forum', 'Report Post'), array('controller' => 'posts', 'action' => 'report', $post_id));
								}

								if ($canReply) {
									$links[] = $this->Html->link(__d('forum', 'Quote'), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'], $post_id));
								}

								if ($links) {
									echo implode(' - ', $links);
								} ?>
							</span>

							<?php if ($settings['enablePostRating'] && (!$hasRated || $settings['showRatingScore'])) { ?>
								<div id="post-ratings-<?php echo $post_id; ?>" class="post-ratings<?php if ($hasRated) echo ' has-rated'; ?>">
									<?php if (!$hasRated) { ?>
										<a href="#up" onclick="return Forum.ratePost(<?php echo $post_id; ?>, 'up');" class="rate-up"><?php echo $this->Html->image('/forum/img/up.png'); ?></a>
									<?php }

									if ($settings['showRatingScore']) { ?>
										<span class="rating"><?php echo number_format($post['Post']['score']); ?></span>
									<?php }

									if (!$hasRated) { ?>
										<a href="#down" onclick="return Forum.ratePost(<?php echo $post_id; ?>, 'down');" class="rate-down"><?php echo $this->Html->image('/forum/img/down.png'); ?></a>
									<?php } ?>
								</div>
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td valign="top" style="width: 25%">
						<h4 class="username"><?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?></h4>

						<?php if (!$isBuried) {
							echo $this->Forum->avatar($post) ?>

							<?php if (!empty($post['User'][$userFields['totalTopics']])) { ?>
								<strong><?php echo __d('forum', 'Total Topics'); ?>:</strong> <?php echo number_format($post['User'][$userFields['totalTopics']]); ?><br>
							<?php } ?>

							<?php if (!empty($post['User'][$userFields['totalPosts']])) { ?>
								<strong><?php echo __d('forum', 'Total Posts'); ?>:</strong> <?php echo number_format($post['User'][$userFields['totalPosts']]); ?>
							<?php }
						} ?>
					</td>
					<td valign="top">
						<div class="post">
							<?php if ($isBuried) { ?>

								<div class="buried-text">
									<?php echo __d('forum', 'This post has been buried.'); ?>
									<a href="javascript:;" onclick="return Forum.toggleBuried(<?php echo $post_id; ?>);"><?php echo __d('forum', 'View the buried post?'); ?></a>
								</div>

								<div class="post-buried" id="post-buried-<?php echo $post_id; ?>" style="display: none">
									<?php echo $this->Decoda->parse($post['Post']['content']); ?>
								</div>

							<?php
							} else {
								echo $this->Decoda->parse($post['Post']['content']);
							} ?>
						</div>

						<?php if (!$isBuried && !empty($post['User'][$userFields['signature']])) { ?>
							<div class="signature">
								<?php echo $this->Decoda->parse($post['User'][$userFields['signature']]); ?>
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

if ($settings['enableQuickReply'] && $canReply) { ?>

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
							<strong><?php echo $this->Form->label('content', __d('forum', 'Message') . ':'); ?></strong><br><br>

							<?php echo $this->Html->link(__d('forum', 'Advanced Reply'), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'])); ?><br>
							<?php echo __d('forum', 'BBCode Enabled'); ?>
						</td>
						<td>
							<?php echo $this->Form->input('content', array(
								'type' => 'textarea',
								'rows' => 5,
								'style' => 'width: 99%',
								'div' => false,
								'error' => false,
								'label' => false
							)); ?>

							<?php echo $this->element('decoda', array('id' => 'PostContent')); ?>
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
