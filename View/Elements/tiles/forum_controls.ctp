<?php if ($user) { ?>
	<div class="controls <?php echo isset($class) ? $class : ''; ?>">
		<?php if ($settings['enable_forum_subscriptions']) {
			if (empty($subscription)) {
				echo $this->Html->link(__d('forum', 'Subscribe'), array('controller' => 'stations', 'action' => 'subscribe', $forum['Forum']['id']), array('class' => 'button subscription', 'onclick' => 'return Forum.subscribe(this);'));
			} else {
				echo $this->Html->link(__d('forum', 'Unsubscribe'), array('controller' => 'stations', 'action' => 'unsubscribe', $subscription['Subscription']['id']), array('class' => 'button subscription', 'onclick' => 'return Forum.unsubscribe(this);'));
			}
		}

		if ($this->Common->hasAccess(AccessLevel::MOD, $forum['Forum']['id'])) {
			echo $this->Html->link(__d('forum', 'Moderate'), array('controller' => 'stations', 'action' => 'moderate', $forum['Forum']['slug']), array('class' => 'button'));
		}

		if ($forum['Forum']['status']) {
			if ($this->Common->hasAccess($forum['Forum']['accessPost'])) {
				echo $this->Html->link(__d('forum', 'Create Topic'), array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug']), array('class' => 'button'));
			}

			if ($this->Common->hasAccess($forum['Forum']['accessPoll'])) {
				echo $this->Html->link(__d('forum', 'Create Poll'), array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug'], 'poll'), array('class' => 'button'));
			}
		} else {
			echo '<span class="button disabled">' . __d('forum', 'Closed') . '</span>';
		} ?>
	</div>
<?php } ?>