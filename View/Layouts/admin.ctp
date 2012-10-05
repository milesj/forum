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
echo $scripts_for_layout; ?>
</head>

<body>
	<div class="wrapper">
		<?php echo $this->element('navigation'); ?>

		<div class="header">
			<?php echo $this->element('search'); ?>

			<h1 class="logo">
				<?php echo $this->Html->link(__d('forum', 'Forum Administration'), $settings['site_main_url']); ?>
			</h1>

			<ul class="menu">
				<li<?php if ($menuTab === 'home') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Home'), array('controller' => 'forum', 'action' => 'index', 'admin' => true)); ?></li>
				<li<?php if ($menuTab === 'settings') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Settings'), array('controller' => 'forum', 'action' => 'settings', 'admin' => true)); ?></li>
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