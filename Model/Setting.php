<?php
/**
 * Forum - Setting
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class Setting extends ForumAppModel {

	/**
	 * Validate.
	 *
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
	 * Return a list of all settings.
	 *
	 * @access public
	 * @return array
	 */
	public function getSettings() {
		return $this->find('list', array(
			'fields' => array('Setting.key', 'Setting.value'),
			'cache' => __METHOD__
		));
	}

	/**
	 * Update all the settings.
	 *
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function update($data) {
		$this->set($data);

		if ($this->validates()) {
			$list = $this->find('list', array(
				'fields' => array('Setting.key', 'Setting.id')
			));

			foreach ($data['Setting'] as $key => $value) {
				$this->id = $list[$key];
				$this->saveField('value', $value);
			}

			return true;
		}

		return false;
	}

}
