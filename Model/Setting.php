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
	 * Return a list of all settings.
	 *
	 * @return array
	 */
	public function getSettings() {
		return $this->find('list', array(
			'fields' => array('Setting.key', 'Setting.value'),
			'cache' => __METHOD__
		));
	}

	/**
	 * Configure the plugin with the database settings.
	 */
	public function configureSettings() {
		if ($settings = $this->getSettings()) {
			$clean = array();

			foreach ($settings as $key => $value) {
				$clean[Inflector::camelize($key)] = $value;
			}

			Configure::write('Forum.settings', Hash::merge(Configure::read('Forum.settings'), $clean));
		}
	}

	/**
	 * Update all the settings.
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function updateSettings($data) {
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
