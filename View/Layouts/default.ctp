<?php echo $this->Html->docType('xhtml-strict'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset(); ?>
<title><?php echo $title_for_layout; ?></title>
<?php
echo $this->Html->css('/forum/css/base.css');
echo $this->Html->css('/forum/css/style.css');
echo $this->Html->script('/forum/js/jquery-1.8.2.min.js');
echo $this->Html->script('/forum/js/forum.js');

if ($this->params['controller'] === 'forum') {
	echo $this->Html->meta(__d('forum', 'RSS Feed - Latest Topics'), array('action' => 'index', 'ext' => 'rss'), array('type' => 'rss'));
} else if (isset($rss)) {
	echo $this->Html->meta(__d('forum', 'RSS Feed - Content Review'), array($rss, 'ext' => 'rss'), array('type' => 'rss'));
}

echo $scripts_for_layout; ?>
</head>

<body>
	<div class="wrapper">
		<?php echo $this->element('navigation'); ?>

		<div class="header">
			<?php echo $this->element('search'); ?>

			<h1 class="logo">
				<?php echo $this->Html->link($settings['site_name'], $settings['site_main_url']); ?>
			</h1>

			<ul class="menu">
				<li<?php if ($menuTab === 'home') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Home'), $settings['site_main_url']); ?></li>
				<li<?php if ($menuTab === 'forums') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Forums'), array('controller' => 'forum', 'action' => 'index')); ?></li>
				<li<?php if ($menuTab === 'search') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?></li>
				<li<?php if ($menuTab === 'rules') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Rules'), array('controller' => 'forum', 'action' => 'rules')); ?></li>
				<li<?php if ($menuTab === 'help') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Help'), array('controller' => 'forum', 'action' => 'help')); ?></li>
				<li<?php if ($menuTab === 'users') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index')); ?></li>

				<?php if ($user && $this->Common->hasAccess(AccessLevel::ADMIN)) { ?>
					<li><?php echo $this->Html->link(__d('forum', 'Admin'), array('controller' => 'forum', 'action' => 'index', 'admin' => true)); ?></li>
				<?php } ?>
			</ul>

			<span class="clear"><!-- --></span>
		</div>

		<div class="content">
			<?php echo $this->element('login'); ?>
			<?php echo $this->element('breadcrumbs'); ?>

			<?php echo $this->Session->flash(); ?>

			<?php echo $content_for_layout; ?>

			<?php echo $this->element('breadcrumbs'); ?>
		</div>

		<div class="footer">
			<?php echo $this->element('copyright'); ?>
		</div>
	</div>

	<?php if (!CakePlugin::loaded('DebugKit')) {
		echo $this->element('sql_dump');
	} ?>
</body>
</html>