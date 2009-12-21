<?php echo $html->docType('xhtml-trans'); ?> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $html->charset(); ?>
<title>
	<?php echo $cupcake->settings['site_name']; ?> - 
	<?php echo $title_for_layout; ?>
</title>

<?php // Scripts
echo $html->css('/forum/css/style.css');
echo $javascript->link('/forum/js/script.js');

if ($this->params['controller'] == 'home') {
	echo $html->meta(__d('forum', 'RSS Feed - Latest Topics', true), array('action' => 'feed', 'ext' => 'rss'), array('type' => 'rss'));
} else if (in_array($this->params['controller'], array('categories', 'topics'))) {
	echo $html->meta(__d('forum', 'RSS Feed - Content Review', true), array('action' => 'feed', $this->params['pass'][0], 'ext' => 'rss'), array('type' => 'rss'));
}

echo $scripts_for_layout; ?>
</head>

<body>
<div id="wrapper">  
	<div id="header">
    	<h1><?php echo $html->link($cupcake->settings['site_name'], $cupcake->settings['site_main_url']); ?></h1>
        
        <ul id="menu">
        	<li<?php if ($menuTab == 'home') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Home', true), array('controller' => 'home', 'action' => 'index')); ?></li>
        	<li<?php if ($menuTab == 'search') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Search', true), array('controller' => 'search', 'action' => 'index')); ?></li>
        	<li<?php if ($menuTab == 'rules') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Rules', true), array('controller' => 'home', 'action' => 'rules')); ?></li>
        	<li<?php if ($menuTab == 'help') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Help', true), array('controller' => 'home', 'action' => 'help')); ?></li>
        	<li<?php if ($menuTab == 'users') echo ' class="active"'; ?>><?php echo $html->link(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'listing')); ?></li>
            <?php if ($cupcake->user() && $cupcake->hasAccess('admin')) { ?>
        	<li><?php echo $html->link(__d('forum', 'Admin', true), array('controller' => 'home', 'action' => 'index', 'admin' => true)); ?></li>
            <?php } ?>
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