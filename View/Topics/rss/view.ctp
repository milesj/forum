<?php

$this->set('channel', array(
	'title' => $settings['name'] . ' - ' . __d('forum', 'Topic') . ' - ' . $topic['Topic']['title'],
	'link' => array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']),
	'description' => $this->Text->truncate($this->Decoda->strip($topic['FirstPost']['content'])),
	'language' => 'en-us'
));

if ($posts) {
	foreach ($posts as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], '#' => 'post-' . $item['Post']['id']);

		echo $this->Rss->item(array(), array(
			'title' => 'Post #' . $item['Post']['id'] . ' - ' . $item['User'][$userFields['username']],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $this->Decoda->parse($item['Post']['content'], array(), false),
			'author' => $item['User'][$userFields['username']],
			'pubDate' => $item['Post']['created']
		));
	}
}