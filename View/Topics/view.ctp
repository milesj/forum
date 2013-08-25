<?php
$this->OpenGraph->description($this->Text->truncate($this->Decoda->strip($topic['FirstPost']['content']), 150));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));

$canReply = ($user && $topic['Topic']['status'] && $this->Forum->hasAccess('Forum.Post', 'create', $topic['Forum']['accessReply'])); ?>

<div class="title">
	<?php echo $this->element('tiles/topic_controls', array('topic' => $topic)); ?>

	<h2>
		<?php if ($topic['Topic']['type'] > Topic::NORMAL) {
			echo '<span>' . $this->Utility->enum('Forum.Topic', 'type', $topic['Topic']['type']) . ':</span> ';

		} else if ($topic['Topic']['status'] == Topic::CLOSED) {
			echo '<span>' . __d('forum', 'Closed') . ':</span> ';
		}

		echo h($topic['Topic']['title']); ?>
	</h2>
</div>

<div class="container">
	<?php if (!empty($topic['Poll']['id'])) { ?>

		<div id="poll" class="panel">
			<div class="panel-head">
				<h5><?php echo __d('forum', 'Poll'); ?></h5>
			</div>

			<div class="panel-body">
				<?php echo $this->Form->create('Poll'); ?>

				<table class="table">
					<tbody>

					<?php if (!$topic['Poll']['hasVoted']) {
						foreach ($topic['Poll']['PollOption'] as $counter => $option) { ?>

						<tr>
							<?php if ($user) { ?>
								<td class="col-icon">
									<input type="radio" name="data[Poll][option]" value="<?php echo $option['id']; ?>"<?php if ($counter == 0) echo ' checked="checked"'; ?>>
								</td>
							<?php } ?>
							<td colspan="2">
								<?php echo $option['option']; ?>
							</td>
						</tr>

						<?php } ?>

						<tr class="divider">
							<td colspan="<?php echo $user ? 3 : 2; ?>" class="align-center">
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

						<tr>
							<td class="align-right">
								<b><?php echo $option['option']; ?></b>
							</td>
							<td style="width: 50%">
								<div class="progress">
									<div class="progress-bar info" style="width: <?php echo $option['percentage']; ?>%"></div>
								</div>
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

	<?php }

	echo $this->element('Admin.pagination', array('class' => 'top')); ?>

	<div class="panel" id="posts">
		<div class="panel-body">
			<table class="table">
				<tbody>
					<?php foreach ($posts as $post) {
						$post_id = $post['Post']['id'];
						$hasRated = in_array($post_id, $ratings);
						$isBuried = ($post['Post']['score'] <= $settings['ratingBuryThreshold']); ?>

					<tr class="<?php if ($isBuried) echo 'is-buried'; ?>" id="post-<?php echo $post_id; ?>">
						<td class="post-time">
							<?php echo $this->Html->tag('time',
								$this->Time->timeAgoInWords($post['Post']['created'], array('timezone' => $this->Forum->timezone())),
								array('class' => 'js-tooltip', 'data-tooltip' => $post['Post']['created'], 'datetime' => $post['Post']['created'])
							); ?>
						</td>
						<td>
							<div class="post-actions">
								<?php
								$links = array();

								if ($user) {
									$isMod = $this->Forum->isMod($topic['Forum']['id']);

									if ($topic['Topic']['firstPost_id'] == $post_id) {
										if ($isMod || ($topic['Topic']['status'] && $user['id'] == $post['Post']['user_id'])) {
											$links[] = $this->Html->link('<span class="icon-wrench"></span>', array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['slug'], (!empty($topic['Poll']['id']) ? 'poll' : '')), array('escape' => false, 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Edit Topic')));
										}

										if ($isMod) {
											$links[] = $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('escape' => false, 'confirm' => __d('forum', 'Are you sure you want to delete?'), 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Delete Topic')));
										}

										$links[] = $this->Html->link('<span class="icon-flag"></span>', array('controller' => 'topics', 'action' => 'report', $topic['Topic']['slug']), array('escape' => false, 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Report Topic')));
									} else {
										if ($isMod || ($topic['Topic']['status'] && $user['id'] == $post['Post']['user_id'])) {
											$links[] = $this->Html->link('<span class="icon-wrench"></span>', array('controller' => 'posts', 'action' => 'edit', $post_id), array('escape' => false, 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Edit Post')));
											$links[] = $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'posts', 'action' => 'delete', $post_id), array('escape' => false, 'confirm' => __d('forum', 'Are you sure you want to delete?'), 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Delete Post')));
										}

										$links[] = $this->Html->link('<span class="icon-flag"></span>', array('controller' => 'posts', 'action' => 'report', $post_id), array('escape' => false, 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Report Post')));
									}

									if ($canReply) {
										$links[] = $this->Html->link('<span class="icon-quote-left"></span>', array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'], $post_id), array('escape' => false, 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Quote')));
									}
								}

								$links[] = $this->Html->link('<span class="icon-link"></span>', '#post-' . $post_id, array('escape' => false, 'class' => 'js-tooltip', 'data-tooltip' => __d('forum', 'Link To This')));

								if ($links) {
									echo implode(' ', $links);
								} ?>
							</div>

							<?php if ($user) {
								if ($settings['enablePostRating'] && (!$hasRated || $settings['showRatingScore'])) { ?>
									<div id="post-ratings-<?php echo $post_id; ?>" class="post-ratings<?php if ($hasRated) echo ' has-rated'; ?>">
										<?php if (!$hasRated) { ?>
											<a href="javascript:;" onclick="return Forum.ratePost(<?php echo $post_id; ?>, 'up');" class="rate-up js-tooltip" data-tooltip="<?php echo __d('forum', 'Rate Up'); ?>">
												<span class="icon-arrow-up"></span>
											</a>
										<?php }

										if ($settings['showRatingScore']) { ?>
											<span class="rating"><?php echo number_format($post['Post']['score']); ?></span>
										<?php }

										if (!$hasRated) { ?>
											<a href="javascript:;" onclick="return Forum.ratePost(<?php echo $post_id; ?>, 'down');" class="rate-down js-tooltip" data-tooltip="<?php echo __d('forum', 'Rate Down'); ?>">
												<span class="icon-arrow-down"></span>
											</a>
										<?php } ?>
									</div>
								<?php } ?>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="span-2">
							<h4 class="username"><?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?></h4>

							<?php if (!$isBuried) {
								echo $this->Forum->avatar($post) ?>

								<?php if (!empty($post['User'][$userFields['totalTopics']])) { ?>
									<b><?php echo __d('forum', 'Total Topics'); ?>:</b> <?php echo number_format($post['User'][$userFields['totalTopics']]); ?><br>
								<?php } ?>

								<?php if (!empty($post['User'][$userFields['totalPosts']])) { ?>
									<b><?php echo __d('forum', 'Total Posts'); ?>:</b> <?php echo number_format($post['User'][$userFields['totalPosts']]); ?>
								<?php }
							} ?>
						</td>
						<td>
							<div class="post">
								<?php if ($isBuried) { ?>

									<div class="buried-text text-muted">
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
		</div>
	</div>

	<?php echo $this->element('Admin.pagination', array('class' => 'bottom'));

	echo $this->element('tiles/topic_controls', array('topic' => $topic));

	if ($settings['enableQuickReply'] && $canReply) { ?>

		<div id="quick-reply" class="panel quick-reply">
			<div class="panel-head">
				<h5><?php echo __d('forum', 'Quick Reply'); ?></h5>
			</div>

			<div class="panel-body">
				<?php
				echo $this->Form->create('Post', array('url' => array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'])));
				echo $this->Form->input('content', array(
					'type' => 'textarea',
					'rows' => 5,
					'div' => false,
					'error' => false,
					'label' => false
				));
				echo $this->element('decoda', array('id' => 'PostContent'));
				echo $this->Form->submit(__d('forum', 'Post Reply'), array('class' => 'button success'));
				echo $this->Form->end(); ?>
			</div>
		</div>

	<?php } ?>
</div>
