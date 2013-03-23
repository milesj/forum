<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

class Forum extends ForumAppModel {

	/**
	 * Behaviors.
	 *
	 * @var array
	 */
	public $actsAs = array(
		'Tree' => array(
			'recursive' => 0
		),
		'Utility.Sluggable' => array(
			'length' => 100
		)
	);

	/**
	 * Belongs to.
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Parent' => array(
			'className' => 'Forum.Forum',
			'foreignKey' => 'parent_id',
			'fields' => array('Parent.id', 'Parent.title', 'Parent.slug', 'Parent.parent_id')
		),
		'LastTopic' => array(
			'className' => 'Forum.Topic',
			'foreignKey' => 'lastTopic_id'
		),
		'LastPost' => array(
			'className' => 'Forum.Post',
			'foreignKey' => 'lastPost_id'
		),
		'LastUser' => array(
			'className' => USER_MODEL,
			'foreignKey' => 'lastUser_id'
		),
		'RequestObject' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'aro_id'
		)
	);

	/**
	 * Has many.
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Topic' => array(
			'className' => 'Forum.Topic',
			'dependent' => false
		),
		'Children' => array(
			'className' => 'Forum.Forum',
			'foreignKey' => 'parent_id',
			'order' => array('Children.orderNo' => 'ASC'),
			'dependent' => false
		),
		'SubForum' => array(
			'className' => 'Forum.Forum',
			'foreignKey' => 'parent_id',
			'order' => array('SubForum.orderNo' => 'ASC'),
			'dependent' => false
		),
		'Moderator' => array(
			'className' => 'Forum.Moderator',
			'dependent' => true,
			'exclusive' => true
		),
		'Subscription' => array(
			'className' => 'Forum.Subscription',
			'exclusive' => true,
			'dependent' => true
		)
	);

	/**
	 * Validate.
	 *
	 * @var array
	 */
	public $validations = array(
		'default' => array(
			'title' => array(
				'rule' => 'notEmpty'
			),
			'description' => array(
				'rule' => 'notEmpty'
			),
			'status' =>  array(
				'rule' => 'notEmpty'
			),
			'orderNo' => array(
				'numeric' => array(
					'rule' => 'numeric'
				),
				'notEmpty' => array(
					'rule' => 'notEmpty'
				)
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @var array
	 */
	public $admin = array(
		'iconClass' => 'icon-list-alt'
	);

	/**
	 * Update all forums by going up the parent chain.
	 *
	 * @param int $id
	 * @param array $data
	 * @return void
	 */
	public function chainUpdate($id, array $data) {
		$this->id = $id;
		$this->save($data, false, array_keys($data));

		$forum = $this->getById($id);

		if ($forum['Forum']['parent_id'] != null) {
			$this->chainUpdate($forum['Forum']['parent_id'], $data);
		}
	}

	/**
	 * Get a forum.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function getBySlug($slug) {
		return $this->find('first', array(
			'conditions' => array(
				'Forum.accessRead' => self::YES,
				'Forum.slug' => $slug
			),
			'contain' => array(
				'Parent',
				'SubForum' => array(
					'conditions' => array(
						'SubForum.accessRead' => self::YES,
						'SubForum.aro_id' => $this->Session->read('Forum.groups')
					),
					'LastTopic', 'LastPost', 'LastUser'
				),
				'Moderator' => array('User')
			),
			'cache' => array(__METHOD__, $slug)
		));
	}

	/**
	 * Get the hierarchy.
	 *
	 * @return array
	 */
	public function getHierarchy() {
		$conditions = array(
			'Forum.aro_id' => $this->Session->read('Forum.groups'),
			'Forum.status' => self::OPEN,
			'Forum.accessRead' => self::YES,
			'OR' => array(
				'Forum.parent_id' => null,
				'Parent.status' => self::OPEN
			)
		);

		$tree = $this->generateTreeList($conditions, null, null, ' -- ');
		$hierarchy = array();
		$parent = null;

		// Reorganize the tree so top level forums are an optgroup
		foreach ($tree as $key => $value) {
			// Child
			if (strpos($value, ' -- ') === 0) {
				$hierarchy[$parent][$key] = substr($value, 4);

			// Parent
			} else {
				$hierarchy[$value] = array();
				$parent = $value;
			}
		}


		return $hierarchy;
	}

	/**
	 * Get the list of forums for the board index.
	 *
	 * @return array
	 */
	public function getIndex() {
		$groups = (array) $this->Session->read('Forum.groups');

		return $this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'conditions' => array(
				'Forum.parent_id' => null,
				'Forum.status' => self::OPEN,
				'Forum.accessRead' => self::YES,
				'Forum.aro_id' => $groups
			),
			'contain' => array(
				'Children' => array(
					'conditions' => array(
						'Children.accessRead' => self::YES,
						'Children.aro_id' => $groups
					),
					'SubForum' => array(
						'fields' => array('SubForum.id', 'SubForum.title', 'SubForum.slug'),
						'conditions' => array(
							'SubForum.accessRead' => self::YES,
							'SubForum.aro_id' => $groups
						)
					),
					'LastTopic', 'LastPost', 'LastUser'
				)
			),
			'cache' => __METHOD__
		));
	}

	/**
	 * Move all categories to a new forum.
	 *
	 * @param int $start_id
	 * @param int $moved_id
	 * @return bool
	 */
	public function moveAll($start_id, $moved_id) {
		return $this->updateAll(
			array('Forum.parent_id' => $moved_id),
			array('Forum.parent_id' => $start_id)
		);
	}

}
