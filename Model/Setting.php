<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
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

}
