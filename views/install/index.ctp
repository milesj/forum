
<div class="forumHeader">
	<h2>Install</h2>
</div>

<p>If this is your first time installing the forum, or you want to install a fresh version with all new tables. Please continue onto the nex step.</p>

<p><button type="button" class="button" onclick="goTo('<?php echo Router::url(array('action' => 'check_database')); ?>');">Begin Installation</button></p>

<br />
<div class="forumHeader">
	<h2>Patch</h2>
</div>

<p>If you already have the forum installed and are now upgrading to a newer version, you will need to patch your installation (if you haven't patched yet).</p>

<p><button type="button" class="button" onclick="goTo('<?php echo Router::url(array('action' => 'patch')); ?>');">Apply Patch</button></p>

<br />
<div class="forumHeader">
	<h2>Create Administrator</h2>
</div>

<p>If you need to create a user and grant admin access, or grant an existent user access, you can do so below.</p>

<p><button type="button" class="button" onclick="goTo('<?php echo Router::url(array('action' => 'create_admin')); ?>');">Grant Access</button></p>

<?php /*
<br />
<div class="forumHeader">
	<h2>Upgrade</h2>
</div>

<p>Certain versions require an upgrade script to upgrade the code correctly. Apply the following updates for the version you want (if you haven't already).</p>

<ul class="decoda_list">
	<li><?php echo $this->Html->link('Upgrade to 1.8', array('action' => 'upgrade_1_8')); ?></li>
</ul>*/ ?>