<?php

if (!isset($document)) {
    $document = array('xmlns:dc' => 'http://purl.org/dc/elements/1.1/');
}

if (!isset($channel)) {
    $channel = array();
}

if (!isset($channel['title'])) {
    $channel['title'] = $this->Breadcrumb->pageTitle($settings['name'], array('separator' => $settings['titleSeparator']));
}

echo $this->Rss->document($document, $this->Rss->channel(array(), $channel, $this->fetch('content')));