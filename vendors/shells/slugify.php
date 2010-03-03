<?php
/**
 * Cupcake - Upgrade to v1.8 Shell
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/forum-plugin
 */

define('FORUM_PLUGIN', dirname(dirname(dirname(__FILE__))) . DS);

class SlugifyShell extends Shell {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Topic', 'Forum.Forum', 'Forum.ForumCategory');

	/**
	 * Update all the tables that have the new slug column, with the slugged version of their title.
	 *
	 * @access public
	 * @return void
	 */
	public function main() {
		$config = $this->__getInstallation();

		if (!$config) {
			$this->out('You must patch your installation before proceeding.');
			return;
		}
		
		$this->out('Commence Slugging');
		$this->out('--------------------');;
		$this->out('');
		
		$this->Topic->tablePrefix = $config['prefix'];
		$this->Forum->tablePrefix = $config['prefix'];
		$this->ForumCategory->tablePrefix = $config['prefix'];

		$slugSettings = array('label' => 'title', 'slug' => 'slug', 'separator' => '-', 'length' => 100, 'overwrite' => false);

		// Loop topics
		$topics = $this->Topic->find('all', array(
			'conditions' => array('Topic.slug' => ''),
			'contain' => false,
			'callbacks' => false,
			'recursive' => -1
		));
		
		if (!empty($topics)) {
			foreach ($topics as $topic) {
				$this->Topic->id = $topic['Topic']['id'];
				$this->Topic->saveField('slug', $this->Topic->Behaviors->Sluggable->__slug($topic['Topic']['title'], $slugSettings));
			}
		}

		$this->out(count($topics) .' topics processed');

		// Loop forums
		$forums = $this->Forum->find('all', array(
			'conditions' => array('Forum.slug' => ''),
			'contain' => false,
			'callbacks' => false,
			'recursive' => -1
		));

		if (!empty($forums)) {
			foreach ($forums as $forum) {
				$this->Forum->id = $forum['Forum']['id'];
				$this->Forum->saveField('slug', $this->Forum->Behaviors->Sluggable->__slug($forum['Forum']['title'], $slugSettings));
			}
		}

		$this->out(count($forums) .' forums processed');

		// Loop forum categories
		$forumCats = $this->ForumCategory->find('all', array(
			'conditions' => array('ForumCategory.slug' => ''),
			'contain' => false,
			'callbacks' => false,
			'recursive' => -1
		));

		if (!empty($forumCats)) {
			foreach ($forumCats as $forumCat) {
				$this->ForumCategory->id = $forumCat['ForumCategory']['id'];
				$this->ForumCategory->saveField('slug', $this->ForumCategory->Behaviors->Sluggable->__slug($forumCat['ForumCategory']['title'], $slugSettings));
			}
		}

		$this->out(count($forumCats) .' forum categories processed');

		$this->out('');
		$this->out('--------------------');
		$this->out('Slugging Complete');
	}

	/**
	 * Load the installation settings.
	 *
	 * @access private
	 * @return array
	 */
	private function __getInstallation() {
		$path = FORUM_PLUGIN .'config'. DS .'install.ini';

		if (file_exists($path)) {
			return parse_ini_file($path);
		}

		return null;
	}
	
}