
<div class="forumHeader">
	<h2><?php __d('forum', 'Edit User'); ?></h2>
</div>

<?php 
echo $this->Form->create('Profile', array('url' => $this->here));
echo $this->Form->input('User.username', array('label' => __d('forum', 'Username', true), 'readonly' => true));
echo $this->Form->input('User.email', array('label' => __d('forum', 'Email', true), 'readonly' => true));
echo $this->Form->input('totalPosts', array('label' => __d('forum', 'Total Posts', true), 'class' => 'numeric'));
echo $this->Form->input('totalTopics', array('label' => __d('forum', 'Total Topics', true), 'class' => 'numeric'));
echo $this->Form->input('locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language', true)));
echo $this->Form->input('timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone', true)));
echo $this->Form->input('signature', array('type' => 'textarea', 'label' => __d('forum', 'Signature', true)));
echo $this->Form->end(__d('forum', 'Update', true)); ?>