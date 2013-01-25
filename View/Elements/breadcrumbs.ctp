
<div class="breadcrumbs">
	<?php echo $this->Html->link($settings['name'], array('controller' => 'forum', 'action' => 'index'));

	if ($crumbs = $this->Breadcrumb->get()) {
		echo ' &raquo; ';

		$count = count($crumbs) - 1;

		foreach ($crumbs as $i => $crumb) {
			echo $this->Html->link($crumb['title'], $crumb['url'], $crumb['options']);

			if ($count != $i) {
				echo ' &raquo; ';
			}
		}
	} ?>
</div>
