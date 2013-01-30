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
				if ($key === 'site_name') {
					$key = 'name';
				} else if ($key === 'site_email') {
					$key = 'email';
				} else if ($key === 'site_main_url') {
					$key = 'url';
				} else if ($key === 'days_till_autolock') {
					$key = 'topicDaysTillAutolock';
				} else {
					$key = lcfirst(Inflector::camelize($key));
				}

				$clean[$key] = $value;
			}

			Configure::write('Forum.settings', Hash::merge(Configure::read('Forum.settings'), $clean));
		}
	}

}
