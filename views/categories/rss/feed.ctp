
<?php // Channel
$this->set('channel', array(
	'title' 		=> $cupcake->settings['site_name'] .' - '. __d('forum', 'Forum', true) .': '. $category['ForumCategory']['title'],
	'link' 			=> array('plugin' => 'forum', 'controller' => 'categories', 'action' => 'view', $category['ForumCategory']['id']),
	'description' 	=> $category['ForumCategory']['description'],
	'language' 		=> 'en-us',
));
			
// Loop rss
if (!empty($items)) {
	foreach ($items as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $item['Topic']['id']);
	
		echo $rss->item(array(), array(
			'title' => $item['Topic']['title'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $decoda->parse($item['FirstPost']['content'], true),
			'dc:creator' => $item['User']['username'],
			'pubDate' => $item['Topic']['created']
		));
	}
}