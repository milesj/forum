<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index')); ?>

<div class="errorPage">
	<h2><?php echo $error->getCode(); ?></h2>
	<h3><?php echo __d('forum', $error->getMessage()); ?></h3>
	<p><b>URL:</b> <?php echo $url; ?></p>
</div>