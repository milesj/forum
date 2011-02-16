
<div class="forumHeader">
	<h2>Patch Installation</h2>
</div>

<?php if ($installed) { ?>

	<?php if (isset($patchMsg)) { ?>
	<div class="successBox">
		<?php echo $patchMsg; ?>
	</div>
	<?php } ?>

	<p>Your forum plugin is up to date, no patch is necessary.</p>

	<p>If you would like to repatch your plugin, please delete the forum/config/install.ini file.</p>

<?php } else { ?>

	<p>If you have upgraded to one of the newer versions, before the Install script was available, some of your data and functionality might fail.</p>

	<p>To fix this you will need to run this patch and apply the functionality.</p>

	<ol id="patchList">
		<li>Select the database that your tables are stored in.</li>
		<li>Did your database table have a prefix?</li>
		<li>Are you using the plugin's user table, or a pre-existent one?</li>
	</ol>

	<?php // Form
	echo $this->Form->create(null, array('action' => 'patch'));
	echo $this->Form->input('database', array('options' => $databases));
	echo $this->Form->input('prefix', array('label' => 'Table Prefix'));
	echo $this->Form->input('user_table', array('type' => 'checkbox', 'label' => 'Using Existent User Table', 'after' => ' Yes'));
	echo $this->Form->end('Apply Patch'); ?>

<?php } ?>