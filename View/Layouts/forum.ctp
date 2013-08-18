<?php
echo $this->Html->docType();
echo $this->OpenGraph->html(); ?>
<head>
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Breadcrumb->pageTitle($settings['name'], array('separator' => $settings['titleSeparator'])); ?></title>
	<?php
	echo $this->Html->css('Admin.titon.min');
	echo $this->Html->css('Admin.font-awesome.min');
	echo $this->Html->css('Admin.style');
	echo $this->Html->css('Forum.style');
	echo $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/mootools/1.4.5/mootools-core-full-nocompat-yc.js');
	echo $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/mootools-more/1.4.0.1/mootools-more-yui-compressed.min.js');
	echo $this->Html->script('Admin.titon.min');
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
	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script'); ?>
</head>
<body class="controller-<?php echo $this->request->controller; ?>">
	<div class="skeleton">
		<header class="head">
			<?php echo $this->element('navigation'); ?>
		</header>

		<div class="body action-<?php echo $this->action; ?>">
			<?php
			$this->Breadcrumb->prepend(__d('forum', 'Forum'), array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'));
			$this->Breadcrumb->prepend($settings['name'], '/');

			echo $this->element('Admin.breadcrumbs');
			echo $this->Session->flash();
			echo $this->fetch('content'); ?>
		</div>

		<footer class="foot">
			<div class="copyright">
				<?php printf(__d('forum', 'Powered by the %s v%s'), $this->Html->link('Forum Plugin', 'http://milesj.me/code/cakephp/forum'), mb_strtoupper($config['Forum']['version'])); ?><br>
				<?php printf(__d('forum', 'Created by %s'), $this->Html->link('Miles Johnson', 'http://milesj.me')); ?>
			</div>

			<?php if (!CakePlugin::loaded('DebugKit')) {
				echo $this->element('sql_dump');
			} ?>
		</footer>
	</div>
</body>
</body>
</html>