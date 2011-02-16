<?php echo $this->Html->docType('xhtml-trans'); ?> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset(); ?>
<title>
	<?php echo $this->Cupcake->settings['site_name']; ?> - 
	<?php echo $title_for_layout; ?>
</title>

<?php // Scripts
echo $this->Html->css('/forum/css/style.css');
echo $this->Html->script('/forum/js/jquery-1.5.min.js');
echo $this->Html->script('/forum/js/script.js');

if ($this->params['controller'] == 'home') {
	echo $this->Html->meta(__d('forum', 'RSS Feed - Latest Topics', true), array('action' => 'feed', 'ext' => 'rss'), array('type' => 'rss'));
} else if (isset($feedId) && in_array($this->params['controller'], array('categories', 'topics'))) {
	echo $this->Html->meta(__d('forum', 'RSS Feed - Content Review', true), array('action' => 'feed', $feedId, 'ext' => 'rss'), array('type' => 'rss'));
}

echo $scripts_for_layout; ?>
</head>

<body>
<div id="wrapper">  
	<div id="header">
    	<h1><?php echo $this->Html->link($this->Cupcake->settings['site_name'], $this->Cupcake->settings['site_main_url']); ?></h1>
        
        <ul id="menu">
        	<li<?php if ($menuTab == 'home') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Home', true), array('controller' => 'home', 'action' => 'index')); ?></li>
        	<li<?php if ($menuTab == 'search') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Search', true), array('controller' => 'search', 'action' => 'index')); ?></li>
        	<li<?php if ($menuTab == 'rules') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Rules', true), array('controller' => 'home', 'action' => 'rules')); ?></li>
        	<li<?php if ($menuTab == 'help') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Help', true), array('controller' => 'home', 'action' => 'help')); ?></li>
        	<li<?php if ($menuTab == 'users') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Users', true), array('controller' => 'users', 'action' => 'listing')); ?></li>
            <?php if ($this->Cupcake->user() && $this->Cupcake->hasAccess('admin')) { ?>
        	<li><?php echo $this->Html->link(__d('forum', 'Admin', true), array('controller' => 'home', 'action' => 'index', 'admin' => true)); ?></li>
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