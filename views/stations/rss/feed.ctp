
<?php // Channel
$this->set('channel', array(
	'title' => $settings['site_name'] .' - '. __d('forum', 'Forum', true) .': '. $forum['Forum']['title'],
	'link' => array('plugin' => 'forum', 'controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']),
	'description' => $forum['Forum']['description'],
	'language' => 'en-us',
));
			
// Loop rss
if (!empty($topics)) {
	foreach ($topics as $topic) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']);
	
		echo $rss->item(array(), array(
			'title' => $topic['Topic']['title'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $this->Decoda->parse($topic['FirstPost']['content'], true),
			'dc:creator' => $topic['User'][$config['userMap']['username']],
			'pubDate' => $topic['Topic']['created']
		));
	}
}