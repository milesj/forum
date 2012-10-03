<?php

$this->set('channel', array(
	'title' => $settings['site_name'] . ' - ' . __d('forum', 'Forum') . ': ' . $forum['Forum']['title'],
	'link' => array('plugin' => 'forum', 'controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']),
	'description' => $forum['Forum']['description'],
	'language' => 'en-us'
));

if ($topics) {
	foreach ($topics as $topic) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']);

		echo $this->Rss->item(array(), array(
			'title' => $topic['Topic']['title'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $topic['FirstPost']['contentHtml'],
			'author' => $topic['User'][$config['userMap']['username']],
			'pubDate' => $topic['Topic']['created']
		));
	}
}