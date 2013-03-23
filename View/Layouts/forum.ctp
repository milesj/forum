<?php
echo $this->Html->docType();
echo $this->OpenGraph->html(array('xmlns' => 'http://www.w3.org/1999/xhtml')); ?>
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Breadcrumb->pageTitle($settings['name'], array('separator' => $settings['titleSeparator'])); ?></title>
	<?php
	echo $this->Html->css('Forum.normalize');
	echo $this->Html->css('Forum.style');
	echo $this->Html->script('Forum.mootools-core-1.4.5');
	echo $this->Html->script('Forum.mootools-more-1.4.0.1');
	echo $this->Html->script('Forum.forum');

	if ($this->params['controller'] === 'forum') {
		echo $this->Html->meta(__d('forum', 'RSS Feed - Latest Topics'), array('action' => 'index', 'ext' => 'rss'), array('type' => 'rss'));
	} else if (isset($rss)) {
		echo $this->Html->meta(__d('forum', 'RSS Feed - Content Review'), array($rss, 'ext' => 'rss'), array('type' => 'rss'));
	}

	$locales = $config['Decoda']['locales'];

	$this->OpenGraph->name($settings['name']);
	$this->OpenGraph->locale(array($locales[Configure::read('Config.language')], $locales[$settings['defaultLocale']]));

	echo $this->OpenGraph->fetch();
	echo $this->fetch('css');
	echo $this->fetch('script'); ?>
</head>

<body>
	<div class="wrapper">
		<?php echo $this->element('navigation'); ?>

		<div class="header">
			<h1 class="logo">
				<?php echo $this->Html->link($settings['name'], $settings['url']); ?>
			</h1>

			<ul class="menu">
				<li<?php if ($menuTab === 'home') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Home'), $settings['url']); ?></li>
				<li<?php if ($menuTab === 'forums') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Forums'), array('controller' => 'forum', 'action' => 'index')); ?></li>
				<li<?php if ($menuTab === 'search') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?></li>
				<li<?php if ($menuTab === 'rules') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Rules'), array('controller' => 'forum', 'action' => 'rules')); ?></li>
				<li<?php if ($menuTab === 'help') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Help'), array('controller' => 'forum', 'action' => 'help')); ?></li>

				<?php if ($user && $this->Forum->isAdmin()) { ?>
					<li><?php echo $this->Html->link(__d('forum', 'Admin'), array('controller' => 'admin', 'action' => 'index', 'plugin' => 'admin', 'admin' => false)); ?></li>
				<?php } ?>
			</ul>

			<span class="clear"><!-- --></span>
		</div>

		<div class="content">
			<?php echo $this->element('search'); ?>
			<?php echo $this->element('breadcrumbs'); ?>

			<span class="clear"></span>

			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>

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