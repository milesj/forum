<?php

$this->Html->addCrumb(__d('forum', 'Administration'), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Settings'), array('controller' => 'forum', 'action' => 'settings')); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Settings'); ?></h2>
</div>

<?php echo $this->Form->create('Setting'); ?>

<div class="container">
	<div class="containerContent">

		<?php // Settings
		echo $this->Form->input('site_name', array('label' => __d('forum', 'Site Name')));
		echo $this->Form->input('site_email', array('label' => __d('forum', 'Site Email')));
		echo $this->Form->input('site_main_url', array('label' => __d('forum', 'Site Website')));
		echo $this->Form->input('security_question', array('label' => __d('forum', 'Security Question')));
		echo $this->Form->input('security_answer', array('label' => __d('forum', 'Security Answer')));
		echo $this->Form->input('title_separator', array('label' => __d('forum', 'Page Title Separator'), 'class' => 'numeric', 'maxlength' => 3)); ?>

		<div class="inputDivider"><?php echo __d('forum', 'Topic Settings'); ?></div>

		<?php // Topic
		echo $this->Form->input('topics_per_page', array('class' => 'numeric', 'label' => __d('forum', 'Topics Per Page')));
		echo $this->Form->input('topics_per_hour', array('class' => 'numeric', 'label' => __d('forum', 'Topics Per Page')));
		echo $this->Form->input('topic_flood_interval', array('class' => 'numeric', 'label' => __d('forum', 'Topic Flood Interval'), 'after' => ' (' . __d('forum', 'Seconds') . ')'));
		echo $this->Form->input('topic_pages_till_truncate', array('class' => 'numeric', 'label' => __d('forum', 'Paging Till Truncation'), 'after' => ' (' . __d('forum', 'Paging under a topic title') . ')')); ?>

		<div class="inputDivider"><?php echo __d('forum', 'Post Settings'); ?></div>

		<?php // Posts
		echo $this->Form->input('posts_per_page', array('class' => 'numeric', 'label' => __d('forum', 'Posts Per Page')));
		echo $this->Form->input('posts_per_hour', array('class' => 'numeric', 'label' => __d('forum', 'Posts Per Hour')));
		echo $this->Form->input('post_flood_interval', array('class' => 'numeric', 'label' => __d('forum', 'Post Flood Interval'), 'after' => ' (' . __d('forum', 'Seconds') . ')'));
		echo $this->Form->input('posts_till_hot_topic', array('class' => 'numeric', 'label' => __d('forum', 'Posts Till Hot Topic'))); ?>

		<div class="inputDivider"><?php echo __d('forum', 'Subscription Settings'); ?></div>

		<?php // Subscriptions
		echo $this->Form->input('enable_topic_subscriptions', array('options' => $this->Common->options(), 'label' => __d('forum', 'Enable Forum Subscriptions')));
		echo $this->Form->input('enable_forum_subscriptions', array('options' => $this->Common->options(), 'label' => __d('forum', 'Enable Topic Subscriptions')));
		echo $this->Form->input('auto_subscribe_self', array('options' => $this->Common->options(), 'label' => __d('forum', 'Auto-subscribe Author To Topic'))); ?>

		<div class="inputDivider"><?php echo __d('forum', 'Misc Settings'); ?></div>

		<?php // Misc
		echo $this->Form->input('default_locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language')));
		echo $this->Form->input('default_timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone')));
		echo $this->Form->input('days_till_autolock', array('class' => 'numeric', 'after' => ' (' . __d('forum', 'Days') . ')', 'label' => __d('forum', 'Inactive Days Till Topic Auto-Lock')));
		echo $this->Form->input('whos_online_interval', array('class' => 'numeric', 'label' => __d('forum', 'Whos Online Interval'), 'after' => ' (' . __d('forum', 'Past Minutes') . ')'));
		echo $this->Form->input('enable_quick_reply', array('options' => $this->Common->options(), 'label' => __d('forum', 'Enable Quick Reply')));
		echo $this->Form->input('enable_gravatar', array('options' => $this->Common->options(), 'label' => __d('forum', 'Enable Gravatar')));
		echo $this->Form->input('censored_words', array('type' => 'textarea', 'label' => __d('forum', 'Censored Words'), 'after' => ' (' . __d('forum', 'Separate with commas') . ')')); ?>

	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Update'), array('class' => 'button'));
echo $this->Form->end(); ?>