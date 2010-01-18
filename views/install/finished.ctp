
<h2>Step 4: Finished</h2>

<p>You have succesfully installed the forum plugin and created the required tables!</p>

<p>If you experience any problems, please check the database manually and check the $tablePrefix property within the AppModel and UserModel classes.</p>

<p>Lastly, you must enable the admin and routes (the script will attempt to but do not rely on it).</p>

<pre class="php decoda_code">
// core.php
Configure::write('Routing.admin', 'admin');

// routes.php
Router::parseExtensions('rss');
Router::connect('/forum', array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index'));
</pre>

<br /><br />

<div class="submit">
	<?php echo $form->button('Go To The Forum', array('onclick' => "goTo('". Router::url(array('controller' => 'home', 'action' => 'index', 'plugin' => 'forum')) ."');")); ?>
</div>