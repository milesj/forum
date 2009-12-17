<?php echo $html->docType('xhtml-trans'); ?> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $html->charset(); ?>
<title>
	<?php __d('forum', 'Cupcake Forum Administration'); ?> -
	<?php echo $title_for_layout; ?>
</title>

<?php echo $html->css('/forum/css/style.css'); ?>
<?php echo $scripts_for_layout; ?>
</head>

<body>
<div id="wrapper">  
	<div id="header">
    	<h1><?php echo $html->link(__d('forum', 'Cupcake Forum Administration', true), $cupcake->settings['site_main_url']); ?></h1>
        
        <ul id="menu">
        	<li<?php if ($menuTab == 'home') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Home', true), array('controller' => 'home', 'action' => 'index', 'admin' => true)); ?></li>
        	<li<?php if ($menuTab == 'settings') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Settings', true), array('controller' => 'home', 'action' => 'settings', 'admin' => true)); ?></li>
        	<li<?php if ($menuTab == 'forums') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Forums', true), array('controller' => 'categories', 'action' => 'index', 'admin' => true)); ?></li>
        	<li<?php if ($menuTab == 'staff') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Staff', true), array('controller' => 'staff', 'action' => 'index', 'admin' => true)); ?></li>
        	<li<?php if ($menuTab == 'reports') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Reported', true), array('controller' => 'reports', 'action' => 'index', 'admin' => true)); ?></li>
        	<li<?php if ($menuTab == 'users') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'index', 'admin' => true)); ?></li>
        	<li><?php echo $html->link(__d('forum', 'Return to Forum', true), array('controller' => 'home', 'action' => 'index', 'admin' => false)); ?></li>
        </ul>
        
        <span class="clear"><!-- --></span>
    </div>
    
    <div id="content">
    	<?php echo $this->element('navigation'); ?>
        
		<?php echo $content_for_layout; ?>
 	</div>
    
    <?php // Would love it if you kept this in all the pages :]
	echo $this->element('copyright'); ?>
</div>    
</body>
</html>