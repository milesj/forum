<?php
class Subscription extends ForumAppModel{
	public $belongsTo = array(
		'Topic' => array(
			'className'		=> 'Forum.Topic',
		),
		'User'
	);
}