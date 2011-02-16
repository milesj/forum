
<div class="forumHeader">
	<h2><?php __d('forum', 'Forum Settings'); ?></h2>
</div>

<?php echo $this->Session->flash(); ?>

<?php // Settings
echo $this->Form->create('Setting', array('url' => array('controller' => 'home', 'action' => 'settings', 'admin' => true)));
echo $this->Form->input('site_name', array('label' => __d('forum', 'Site Name', true)));
echo $this->Form->input('site_email', array('label' => __d('forum', 'Site Email', true)));
echo $this->Form->input('site_main_url', array('label' => __d('forum', 'Site Website', true)));
echo $this->Form->input('security_question', array('label' => __d('forum', 'Security Question', true)));
echo $this->Form->input('security_answer', array('label' => __d('forum', 'Security Answer', true))); ?>

<p><strong><?php __d('forum', 'Topic Settings'); ?></strong></p>

<?php // Topic
echo $this->Form->input('topics_per_page', array('style' => 'width: 50px', 'label' => __d('forum', 'Topics Per Page', true)));
echo $this->Form->input('topics_per_hour', array('style' => 'width: 50px', 'label' => __d('forum', 'Topics Per Page', true)));
echo $this->Form->input('topic_flood_interval', array('style' => 'width: 50px', 'label' => __d('forum', 'Topic Flood Interval', true), 'after' => ' ('. __d('forum', 'Seconds', true) .')'));
echo $this->Form->input('topic_pages_till_truncate', array('style' => 'width: 50px', 'label' => __d('forum', 'Paging Till Truncation', true), 'after' => ' ('. __d('forum', 'Paging under a topic title', true) .')')); ?>

<p><strong><?php __d('forum', 'Post Settings'); ?></strong></p>

<?php // Posts
echo $this->Form->input('posts_per_page', array('style' => 'width: 50px', 'label' => __d('forum', 'Posts Per Page', true)));
echo $this->Form->input('posts_per_hour', array('style' => 'width: 50px', 'label' => __d('forum', 'Posts Per Hour', true)));
echo $this->Form->input('post_flood_interval', array('style' => 'width: 50px', 'label' => __d('forum', 'Post Flood Interval', true), 'after' => ' ('. __d('forum', 'Seconds', true) .')'));
echo $this->Form->input('posts_till_hot_topic', array('style' => 'width: 50px', 'label' => __d('forum', 'Posts Till Hot Topic', true))); ?>

<p><strong><?php __d('forum', 'Misc Settings'); ?></strong></p>

<?php // Misc
echo $this->Form->input('default_locale', array('options' => $this->Cupcake->getLocales(), 'label' => __d('forum', 'Language', true)));
echo $this->Form->input('days_till_autolock', array('style' => 'width: 50px', 'after' => ' ('. __d('forum', 'Days', true) .')', 'label' => __d('forum', 'Inactive Days Till Topic Auto-Lock', true)));
echo $this->Form->input('whos_online_interval', array('style' => 'width: 50px', 'label' => __d('forum', 'Whos Online Interval', true), 'after' => ' ('. __d('forum', 'Past Minutes', true) .')'));
echo $this->Form->input('enable_quick_reply', array('options' => $this->Cupcake->options(), 'label' => __d('forum', 'Enable Quick Reply', true)));
echo $this->Form->input('enable_gravatar', array('options' => $this->Cupcake->options(), 'label' => __d('forum', 'Enable Gravatar', true)));
echo $this->Form->input('censored_words', array('type' => 'textarea', 'label' => __d('forum', 'Censored Words', true), 'after' => ' ('. __d('forum', 'Separate with commas', true) .')')); ?>

<?php echo $this->Form->end(__d('forum', 'Update', true)); ?>