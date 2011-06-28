
<div class="forumHeader">
	<h2><?php __d('forum', 'Edit Profile'); ?></h2>
</div>

<?php 
echo $this->Form->create('Profile', array('url' => $this->here));
echo $this->Form->input('locale', array('options' => $config['locales'], 'label' => __d('forum', 'Language', true)));
echo $this->Form->input('timezone', array('options' => $config['timezones'], 'label' => __d('forum', 'Timezone', true))); ?>

<div class="input textarea">
	<?php echo $this->Form->label('signature', __d('forum', 'Signature', true)); ?>

	<div id="textarea">
		<?php echo $this->Form->input('signature', array('type' => 'textarea', 'rows' => 5, 'label' => false, 'div' => false)); ?>
	</div>

	<span class="clear"><!-- --></span>
	<?php echo $this->element('markitup', array('textarea' => 'UserSignature')); ?>
</div>

<?php echo $this->Form->end(__d('forum', 'Update Account', true)); ?>
