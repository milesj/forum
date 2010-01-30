<?php
/**
 * Cupcake - Upgrade to v1.8 Shell
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/forum-plugin
 */

class Upgrade18Shell extends Shell {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Topic', 'Forum', 'ForumCategory');

	/**
	 * Update all the tables that have the new slug column, with the slugged version of their title.
	 *
	 * @access public
	 * @return void
	 */
	public function main() {
		$this->out('Upgrading to 1.8');
		$this->out('--------------------');
		$this->out('Commence Slugging...');

		$slugSettings = array('label' => 'title', 'slug' => 'slug', 'separator' => '-', 'length' => 100, 'overwrite' => false);

		// Loop topics
		$topics = $this->Topic->find('all', array(
			'conditions' => array('Topic.slug !=' => ''),
			'contain' => false,
			'callbacks' => false,
			'recursive' => -1
		));
		
		if (!empty($topics)) {
			foreach ($topics as $topic) {
				$slug = $this->Topic->Behaviors->Sluggable->__slug($topic['Topic']['title'], $slugSettings);
				
				$this->Topic->id = $topic['Topic']['id'];
				$this->Topic->save(array('slug' => $slug), false, array('slug'));
			}
		}

		$this->out(count($topics) .' topics processed');

		// Loop forums
		$forums = $this->Forum->find('all', array(
			'conditions' => array('Forum.slug !=' => ''),
			'contain' => false,
			'callbacks' => false,
			'recursive' => -1
		));

		if (!empty($forums)) {
			foreach ($forums as $forum) {
				$slug = $this->Forum->Behaviors->Sluggable->__slug($forum['Forum']['title'], $slugSettings);
				
				$this->Forum->id = $forum['Forum']['id'];
				$this->Forum->save(array('slug' => $slug), false, array('slug'));
			}
		}

		$this->out(count($forums) .' forums processed');

		// Loop forum categories
		$forumCats = $this->ForumCategory->find('all', array(
			'conditions' => array('ForumCategory.slug !=' => ''),
			'contain' => false,
			'callbacks' => false,
			'recursive' => -1
		));

		if (!empty($forumCats)) {
			foreach ($forumCats as $forumCat) {
				$slug = $this->ForumCategory->Behaviors->Sluggable->__slug($forumCat['ForumCategory']['title'], $slugSettings);

				$this->ForumCategory->id = $forumCat['ForumCategory']['id'];
				$this->ForumCategory->save(array('slug' => $slug), false, array('slug'));
			}
		}

		$this->out(count($forumCats) .' forum categories processed');
		
		$this->out('...Slugging Complete');
		$this->out('--------------------');
		$this->out('You are now upgraded to 1.8');
	}
	
}