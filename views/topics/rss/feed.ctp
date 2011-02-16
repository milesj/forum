
<?php // Channel
$this->set('channel', array(
	'title' 		=> $this->Cupcake->settings['site_name'] .' - '. __d('forum', 'Topic', true) .': '. $topic['Topic']['title'],
	'link' 			=> array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']),
	'description' 	=> $this->Text->truncate($this->Decoda->parse($topic['FirstPost']['content'], true)),
	'language' 		=> 'en-us',
));
			
// Loop rss
if (!empty($items)) {
	foreach ($items as $item) {
		$link = array('plugin' => 'forum', 'controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'], '#' => 'post_'. $item['Post']['id']);
	
		echo $rss->item(array(), array(
			'title' => 'Post #'. $item['Post']['id'] .' - '. $item['User']['username'],
			'link' => $link,
			'guid' => array('url' => $link, 'isPermaLink' => 'true'),
			'description' => $this->Decoda->parse($item['Post']['content'], true),
			'dc:creator' => $item['User']['username'],
			'pubDate' => $item['Post']['created']
		));
	}
}