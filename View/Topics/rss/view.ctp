<?php

$this->set('channel', array(
	'title' => $settings['site_name'] . ' - ' . __d('forum', 'Topic') . ': ' . $topic['Topic']['title'],
	'link' => array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']),
	'description' => $this->Text->truncate(strip_tags($topic['FirstPost']['contentHtml'])),
	'language' => 'en-us'
));

if ($posts) {
	foreach ($posts as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], '#' => 'post-' . $item['Post']['id']);

		echo $this->Rss->item(array(), array(
			'title' => 'Post #' . $item['Post']['id'] . ' - ' . $item['User'][$config['userMap']['username']],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $item['Post']['contentHtml'],
			'author' => $item['User'][$config['userMap']['username']],
			'pubDate' => $item['Post']['created']
		));
	}
}