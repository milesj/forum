
<div class="forumHeader">
	<h2><?php __d('forum', 'Edit Profile'); ?></h2>
</div>

<?php echo $this->Session->flash(); ?>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit'))); ?>
<?php echo $this->Form->input('email', array('label' => __d('forum', 'Email', true))); ?>
<?php echo $this->Form->input($this->Cupcake->columnMap['locale'], array('options' => $this->Cupcake->getLocales(), 'label' => __d('forum', 'Language', true))); ?>
<?php echo $this->Form->input($this->Cupcake->columnMap['timezone'], array('options' => $this->Cupcake->getTimezones(), 'label' => __d('forum', 'Timezone', true))); ?>

<div class="input textarea">
	<?php echo $this->Form->label($this->Cupcake->columnMap['signature'], __d('forum', 'Signature', true)); ?>

	<div id="textarea">
		<?php echo $this->Form->input($this->Cupcake->columnMap['signature'], array('type' => 'textarea', 'rows' => 5, 'label' => false, 'div' => false)); ?>
	</div>

	<span class="clear"><!-- --></span>
	<?php echo $this->element('markitup', array('textarea' => 'UserSignature')); ?>
</div>

<?php echo $this->Form->end(__d('forum', 'Update Account', true)); ?>

<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'edit'))); ?>
<?php echo $this->Form->input('oldPassword', array('type' => 'password', 'label' => __d('forum', 'Old Password', true))); ?>
<?php echo $this->Form->input('newPassword', array('type' => 'password', 'label' => __d('forum', 'New Password', true))); ?>
<?php echo $this->Form->input('confirmPassword', array('type' => 'password', 'label' => __d('forum', 'Confirm Password', true))); ?>
<?php echo $this->Form->end(__d('forum', 'Update Password', true)); ?>