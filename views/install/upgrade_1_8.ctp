
<div class="forumHeader">
	<h2>Upgrade to 1.8</h2>
</div>

<?php if (isset($upgraded)) { ?>

	<p>You are now upgraded to 1.8! Enjoy!</p>

<?php } else { ?>

	<p>You are about to upgrade to the 1.8 version of this plugin. The update will process the following:</p>

	<ul class="decoda_list">
		<li>Adding the new "slug" column to specific tables.</li>
		<li>Update the slug column in all the topics and forums. (Can be a huge process if there are tons of rows)
	</ul>

	<br />
	<p><b>Note:</b> The database <i><?php echo $config['database']; ?></i> and prefix <i><?php echo $config['prefix']; ?></i> will be used for the table alterations.</p>

	<?php // Form
	echo $form->create(null, array('action' => 'upgrade_1_8'));
	echo $form->end('Commence Upgrade'); ?>

<?php } ?>