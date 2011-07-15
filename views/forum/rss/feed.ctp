<?php 

$this->set('channel', array(
	'title' => $settings['site_name'] .' - '. __d('forum', 'Latest Topics', true),
	'link' => array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index'),
	'description' => __d('forum', 'The latest 10 topics out of all forums', true),
	'language' => 'en-us'
));

if (!empty($items)) {
	foreach ($items as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $item['Topic']['slug']);
	
		echo $this->Rss->item(array(), array(
			'title' => $item['Topic']['title'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $this->Decoda->parse($item['FirstPost']['content'], true),
			'dc:creator' => $item['User'][$config['userMap']['username']],
			'pubDate' => $item['Topic']['created']
		));
	}
}