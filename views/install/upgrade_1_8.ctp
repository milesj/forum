
<div class="forumHeader">
	<h2>Upgrade to 1.8</h2>
</div>

<?php if (isset($upgraded)) { ?>

	<p>You are now upgraded to 1.8! However, you are not finished yet, time for the hard part.</p>

	<p>Since we added a new slugged title feature, we must now go and process all the old rows and update them with their slugs.
		To do this we must use the Cake console and run the the shell <b>slugify</b>.
		Your shell command may look like the following (Make sure you are running the plugin shell!)</p>

	<pre class="php decoda_code">cake -app /path/to/app slugify</pre>

<?php } else { ?>

	<p>You are about to upgrade to the 1.8 version of this plugin. The update will process the following:</p>

	<ul class="decoda_list">
		<li>Adding the new "slug" column to specific tables.</li>
	</ul>

	<br />
	<p><b>Note:</b> The database <i><?php echo $config['database']; ?></i> and prefix <i><?php echo $config['prefix']; ?></i> will be used for the table alterations.</p>

	<?php // Form
	echo $form->create(null, array('action' => 'upgrade_1_8'));
	echo $form->end('Commence Upgrade'); ?>

<?php } ?>