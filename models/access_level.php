<?php
/** 
 * Forum - AccessLevel Model
 *
 * @author		Miles Johnson - http://milesj.me
 * @copyright	Copyright 2006-2010, Miles Johnson, Inc.
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		http://milesj.me/resources/script/forum-plugin
 */
 
class AccessLevel extends ForumAppModel {
	
	/**
	 * Validation.
	 *
	 * @access public
	 * @var array
	 */
	public $validate = array(
		'level' => 'notEmpty',
		'title' => 'notEmpty'
	);

	/**
	 * Get a list of levels.
	 *
	 * @access public
	 * @param int $exclude
	 * @return array
	 */
	public function getHigherLevels($exclude = null) {
		$conditions = array('AccessLevel.level >' => 1);
		
		if (is_numeric($exclude)) {
			$conditions['AccessLevel.id !='] = $exclude;
		}
		
		return $this->find('list', array(
			'fields' => array('AccessLevel.id', 'AccessLevel.title'),
			'conditions' => $conditions
		));
	}

	/**
	 * Get a list of levels.
	 * 
	 * @access public
	 * @return array
	 */
	public function getList() {
		return $this->find('all', array(
			'order' => 'AccessLevel.level ASC'
		));
	}
	
}
