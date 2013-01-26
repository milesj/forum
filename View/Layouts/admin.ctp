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

	echo $this->OpenGraph->fetch();
	echo $this->fetch('css');
	echo $this->fetch('script'); ?>
</head>

<body>
	<div class="wrapper">
		<?php echo $this->element('navigation'); ?>

		<div class="header">
			<h1 class="logo">
				<?php echo $this->Html->link(__d('forum', 'Forum Administration'), $settings['url']); ?>
			</h1>

			<ul class="menu">
				<li<?php if ($menuTab === 'home') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Home'), array('controller' => 'forum', 'action' => 'index', 'admin' => true)); ?></li>
				<li<?php if ($menuTab === 'forums') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Forums'), array('controller' => 'stations', 'action' => 'index', 'admin' => true)); ?></li>
				<li<?php if ($menuTab === 'staff') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Staff'), array('controller' => 'staff', 'action' => 'index', 'admin' => true)); ?></li>
				<li<?php if ($menuTab === 'reports') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Reported'), array('controller' => 'reports', 'action' => 'index', 'admin' => true)); ?></li>
				<li<?php if ($menuTab === 'users') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Users'), array('controller' => 'users', 'action' => 'index', 'admin' => true)); ?></li>
				<li><?php echo $this->Html->link(__d('forum', 'Return to Forum'), array('controller' => 'forum', 'action' => 'index', 'admin' => false)); ?></li>
			</ul>

			<span class="clear"><!-- --></span>
		</div>

		<div class="content">
			<?php echo $this->element('breadcrumbs'); ?>

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