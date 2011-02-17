
<div class="forumHeader">
	<h2>Step 4: Finished</h2>
</div>

<p>You have succesfully installed the forum plugin and created the required tables!</p>

<p>If you experience any problems, please check the database manually and check the $tablePrefix property within the AppModel and UserModel classes.</p>

<p>Lastly, you must enable the admin and routes (the script will attempt to, but do not rely on it).</p>

<pre class="php decoda_code">
// core.php
Configure::write('Routing.prefixes', array('admin')); // 1.3

// routes.php
Router::parseExtensions('rss');
Router::connect('/forum', array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index'));
</pre>

<br /><br />

<div class="submit">
	<?php echo $this->Form->button('Visit Forum', array('onclick' => "goTo('". Router::url(array('controller' => 'home', 'action' => 'index', 'plugin' => 'forum')) ."');", 'type' => 'button', 'class' => 'button')); ?>
	<?php echo $this->Form->button('Create Admin', array('onclick' => "goTo('". Router::url(array('action' => 'create_admin')) ."');", 'type' => 'button', 'class' => 'button')); ?>
</div>