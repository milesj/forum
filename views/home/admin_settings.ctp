
<h2><?php __d('forum', 'Forum Settings'); ?></h2>

<?php $session->flash(); ?>

<?php // Settings
echo $form->create('Setting', array('url' => array('controller' => 'home', 'action' => 'settings', 'admin' => true)));
echo $form->input('site_name', array('label' => __d('forum', 'Site Name', true)));
echo $form->input('site_email', array('label' => __d('forum', 'Site Email', true)));
echo $form->input('site_main_url', array('label' => __d('forum', 'Site Website', true)));
echo $form->input('security_question', array('label' => __d('forum', 'Security Question', true)));
echo $form->input('security_answer', array('label' => __d('forum', 'Security Answer', true))); ?>

<p><strong><?php __d('forum', 'Topic Settings'); ?></strong></p>

<?php // Topic
echo $form->input('topics_per_page', array('style' => 'width: 50px', 'label' => __d('forum', 'Topics Per Page', true)));
echo $form->input('topics_per_hour', array('style' => 'width: 50px', 'label' => __d('forum', 'Topics Per Page', true)));
echo $form->input('topic_flood_interval', array('style' => 'width: 50px', 'label' => __d('forum', 'Topic Flood Interval', true), 'after' => ' ('. __d('forum', 'Seconds', true) .')'));
echo $form->input('topic_pages_till_truncate', array('style' => 'width: 50px', 'label' => __d('forum', 'Paging Till Truncation', true), 'after' => ' ('. __d('forum', 'Paging under a topic title', true) .')')); ?>

<p><strong><?php __d('forum', 'Post Settings'); ?></strong></p>

<?php // Posts
echo $form->input('posts_per_page', array('style' => 'width: 50px', 'label' => __d('forum', 'Posts Per Page', true)));
echo $form->input('posts_per_hour', array('style' => 'width: 50px', 'label' => __d('forum', 'Posts Per Hour', true)));
echo $form->input('post_flood_interval', array('style' => 'width: 50px', 'label' => __d('forum', 'Post Flood Interval', true), 'after' => ' ('. __d('forum', 'Seconds', true) .')'));
echo $form->input('posts_till_hot_topic', array('style' => 'width: 50px', 'label' => __d('forum', 'Posts Till Hot Topic', true))); ?>

<p><strong><?php __d('forum', 'Misc Settings'); ?></strong></p>

<?php // Misc
echo $form->input('days_till_autolock', array('style' => 'width: 50px', 'after' => ' ('. __d('forum', 'Days', true) .')', 'label' => __d('forum', 'Inactive Days Till Topic Auto-Lock', true)));
echo $form->input('whos_online_interval', array('style' => 'width: 50px', 'label' => __d('forum', 'Whos Online Interval', true), 'after' => ' ('. __d('forum', 'Past Minutes', true) .')'));
echo $form->input('enable_quick_reply', array('options' => $cupcake->options(), 'label' => __d('forum', 'Enable Quick Reply', true)));
echo $form->input('enable_gravatar', array('options' => $cupcake->options(), 'label' => __d('forum', 'Enable Gravatar', true)));
echo $form->input('censored_words', array('type' => 'textarea', 'label' => __d('forum', 'Censored Words', true), 'after' => ' ('. __d('forum', 'Separate with commas', true) .')')); ?>

<?php echo $form->end(__d('forum', 'Update', true)); ?>