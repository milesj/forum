
<?php // Channel
$this->set('channel', array(
	'title' 		=> $this->Cupcake->settings['site_name'] .' - '. __d('forum', 'Latest Topics', true),
	'link' 			=> array('plugin' => 'forum', 'controller' => 'home', 'action' => 'index'),
	'description' 	=> __d('forum', 'The latest 10 topics out of all forums', true),
	'language' 		=> 'en-us',
));
			
// Loop rss
if (!empty($items)) {
	foreach ($items as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $item['Topic']['slug']);
	
		echo $rss->item(array(), array(
			'title' => $item['Topic']['title'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $this->Decoda->parse($item['FirstPost']['content'], true),
			'dc:creator' => $item['User']['username'],
			'pubDate' => $item['Topic']['created']
		));
	}
}