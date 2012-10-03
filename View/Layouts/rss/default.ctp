<?php

if (!isset($document)) {
    $document = array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/');
}

if (!isset($channel)) {
    $channel = array();
}

if (!isset($channel['title'])) {
    $channel['title'] = $title_for_layout;
}

echo $this->Rss->document($document, $this->Rss->channel(array(), $channel, $content_for_layout));