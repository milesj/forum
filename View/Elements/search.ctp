
<div class="search">
	<?php 
	echo $this->Form->create('Search', array('url' => array('controller' => 'search', 'action' => 'proxy')));
	echo $this->Form->input('keywords', array('label' => false));
	echo $this->Form->submit(__d('forum', 'Search'), array('class' => 'buttonSmall'));
	echo $this->Form->end(); ?>
</div>