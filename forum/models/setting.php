<?php
/** 
 * setting.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - Setting Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class Setting extends ForumAppModel {

	/**
	 * No table
	 * @access public
	 * @var boolean
	 */
	public $useTable = false;
	
	/**
	 * Validate
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'site_name' => 'notEmpty',
		'site_email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Please supply a valid email address'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'site_main_url' => array(
			'url' => array(
				'rule' => array('url', true),
				'message' => 'Please supply a valid URL'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'security_question' => 'notEmpty',
		'security_answer' => 'notEmpty',
		'topics_per_page' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'topics_per_hour' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'topic_flood_interval' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'topic_pages_till_truncate' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'posts_per_page' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'posts_per_hour' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'posts_till_hot_topic' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'post_flood_interval' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'days_till_autolock' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'whos_online_interval' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Please supply a number'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This setting is required'
			)
		),
		'supported_locales' => 'notEmpty',
	);
	
	/**
	 * Save the data to the ini file
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function process($data) {
		$settings = array();
		foreach ($data['Setting'] as $field => $value) {
			$value = htmlentities(strip_tags(trim($value)), ENT_NOQUOTES, 'UTF-8');
			if (!is_numeric($value)) {
				$value = '"'. $value .'"';
			}
			$settings[] = $field .' = '. $value;
		}
		
		$path = APP .'plugins'. DS .'forum'. DS .'config'. DS .'settings.ini';
		$handle = fopen($path, "w");
		fwrite($handle, implode("\n", $settings));
		fclose($handle);
		chmod($path, 0777);
		
		return true;
	}
	
}
