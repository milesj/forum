<?php

$this->set('channel', array(
	'title' => $settings['name'] . ' - ' . __d('forum', 'Latest Topics'),
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
			'description' => $this->Decoda->parse($item['FirstPost']['content'], array(), false),
			'author' => $item['User'][$userFields['username']],
			'pubDate' => $item['Topic']['created']
		));
	}
}