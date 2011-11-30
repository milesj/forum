<?php 

$this->Html->addCrumb(__d('forum', 'Administration', true), array('controller' => 'forum', 'action' => 'index'));
$this->Html->addCrumb(__d('forum', 'Settings', true), array('controller' => 'forum', 'action' => 'settings')); ?>

<div class="title">
	<h2><?php __d('forum', 'Settings'); ?></h2>
</div>

<?php echo $this->Form->create('Setting', array('url' => $this->here)); ?>

<div class="container">
	<div class="containerContent">

		<?php // Settings
		echo $this->Form->input('site_name', array('label' => __d('forum', 'Site Name', true)));
		echo $this->Form->input('site_email', array('label' => __d('forum', 'Site Email', true)));
		echo $this->Form->input('site_main_url', array('label' => __d('forum', 'Site Website', true)));
		echo $this->Form->input('security_question', array('label' => __d('forum', 'Security Question', true)));
		echo $this->Form->input('security_answer', array('label' => __d('forum', 'Security Answer', true)));
		echo $this->Form->input('title_separator', array('label' => __d('forum', 'Page Title Separator', true), 'class' => 'numeric', 'maxlength' => 3)); ?>

		<div class="inputDivider"><?php __d('forum', 'Topic Settings'); ?></div>

		<?php // Topic
		echo $this->Form->input('topics_per_page', array('class' => 'numeric', 'label' => __d('forum', 'Topics Per Page', true)));
		echo $this->Form->input('topics_per_hour', array('class' => 'numeric', 'label' => __d('forum', 'Topics Per Page', true)));
		echo $this->Form->input('topic_flood_interval', array('class' => 'numeric', 'label' => __d('forum', 'Topic Flood Interval', true), 'after' => ' ('. __d('forum', 'Seconds', true) .')'));
		echo $this->Form->input('topic_pages_till_truncate', array('class' => 'numeric', 'label' => __d('forum', 'Paging Till Truncation', true), 'after' => ' ('. __d('forum', 'Paging under a topic title', true) .')')); ?>

		<div class="inputDivider"><?php __d('forum', 'Post Settings'); ?></div>

		<?php // Posts
		echo $this->Form->input('posts_per_page', array('class' => 'numeric', 'label' => __d('forum', 'Posts Per Page', true)));
		echo $this->Form->input('posts_per_hour', array('class' => 'numeric', 'label' => __d('forum', 'Posts Per Hour', true)));
		echo $this->Form->input('post_flood_interval', array('class' => 'numeric', 'label' => __d('forum', 'Post Flood Interval', true), 'after' => ' ('. __d('forum', 'Seconds', true) .')'));
		echo $this->Form->input('posts_till_hot_topic', array('class' => 'numeric', 'label' => __d('forum', 'Posts Till Hot Topic', true))); ?>

		<div class="inputDivider"><?php __d('forum', 'Misc Settings'); ?></div>

		<?php // Misc
		echo $this->Form->input('default_locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language', true)));
		echo $this->Form->input('default_timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone', true)));
		echo $this->Form->input('days_till_autolock', array('class' => 'numeric', 'after' => ' ('. __d('forum', 'Days', true) .')', 'label' => __d('forum', 'Inactive Days Till Topic Auto-Lock', true)));
		echo $this->Form->input('whos_online_interval', array('class' => 'numeric', 'label' => __d('forum', 'Whos Online Interval', true), 'after' => ' ('. __d('forum', 'Past Minutes', true) .')'));
		echo $this->Form->input('enable_quick_reply', array('options' => $this->Common->options(), 'label' => __d('forum', 'Enable Quick Reply', true)));
		echo $this->Form->input('enable_gravatar', array('options' => $this->Common->options(), 'label' => __d('forum', 'Enable Gravatar', true)));
		echo $this->Form->input('censored_words', array('type' => 'textarea', 'label' => __d('forum', 'Censored Words', true), 'after' => ' ('. __d('forum', 'Separate with commas', true) .')')); ?>


		<div class="inputDivider"><?php __d('forum', 'Subscriptions Settings'); ?></div>
		<?php
		
		echo $this->Form->input('enable_subscriptions', array('type' => 'select','options' => $this->Common->options())); 
		echo $this->Form->input('enable_forum_subscriptions', array('type' => 'select','options' => $this->Common->options())); 
		echo $this->Form->input('subscription_email_topic_subject');
		echo $this->Form->input('subscription_email_post_subject');
		echo $this->Form->input('auto_subscribe_self', array('type' => 'select','options' => $this->Common->options())); 

		?>
	</div>
</div>

<?php 
echo $this->Form->submit(__d('forum', 'Update', true), array('class' => 'button'));
echo $this->Form->end(); ?>