<?php if ($user) { ?>
	<div class="controls">
		<?php if ($settings['enable_topic_subscriptions']) {
			if (empty($subscription)) {
				echo $this->Html->link(__d('forum', 'Subscribe', true), array('controller' => 'topics', 'action' => 'subscribe', $topic['Topic']['id']), array('class' => 'button subscription', 'onclick' => 'return Forum.subscribe(this);'));
			} else {
				echo $this->Html->link(__d('forum', 'Unsubscribe', true), array('controller' => 'topics', 'action' => 'unsubscribe', $subscription['Subscription']['id']), array('class' => 'button subscription', 'onclick' => 'return Forum.unsubscribe(this);'));
			} 
		}
		
		if ($this->Common->hasAccess(AccessLevel::MOD, $topic['Forum']['id'])) {
			echo $this->Html->link(__d('forum', 'Moderate', true), array('controller' => 'topics', 'action' => 'moderate', $topic['Topic']['slug']), array('class' => 'button'));
		} 
		
		if ($this->Common->hasAccess($topic['Forum']['accessPost'])) {
			echo $this->Html->link(__d('forum', 'Create Topic', true), array('controller' => 'topics', 'action' => 'add', $topic['Forum']['slug']), array('class' => 'button'));
		}
		
		if ($this->Common->hasAccess($topic['Forum']['accessPoll'])) {
			echo $this->Html->link(__d('forum', 'Create Poll', true), array('controller' => 'topics', 'action' => 'add', $topic['Forum']['slug'], 'poll'), array('class' => 'button'));
		}
		
		if ($this->Common->hasAccess($topic['Forum']['accessReply'])) {
			if ($topic['Topic']['status']) {
				echo $this->Html->link(__d('forum', 'Post Reply', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug']), array('class' => 'button'));
			} else {
				echo '<span class="button disabled">'. __d('forum', 'Closed', true) .'</span>';
			}	
		} ?>
	</div>
<?php } ?>