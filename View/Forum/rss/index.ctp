<?php

$this->set('channel', array(
	'title' => $settings['site_name'] . ' - ' . __d('forum', 'Latest Topics'),
	'link' => array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index'),
	'description' => __d('forum', 'The latest 10 topics out of all forums'),
	'language' => 'en-us'
));

if ($items) {
	foreach ($items as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $item['Topic']['slug']);

		echo $this->Rss->item(array(), array(
			'title' => $item['Topic']['title'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $item['FirstPost']['contentHtml'],
			'author' => $item['User'][$config['userMap']['username']],
			'pubDate' => $item['Topic']['created']
		));
	}
}