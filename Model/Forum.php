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
		'Tree',
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
		return $this->filterByRole($this->find('first', array(
			'conditions' => array(
				'Forum.accessRead' => self::YES,
				'Forum.slug' => $slug
			),
			'contain' => array(
				'Parent',
				'SubForum' => array(
					'conditions' => array(
						'SubForum.status' => self::OPEN,
						'SubForum.accessRead' => self::YES
					),
					'LastTopic', 'LastPost', 'LastUser'
				),
				'Moderator' => array('User')
			),
			'cache' => array(__METHOD__, $slug)
		)));
	}

	/**
	 * Get the tree and reorganize into a hierarchy.
	 * Code borrowed from TreeBehavior::generateTreeList().
	 *
	 * @return array
	 */
	public function getHierarchy() {
		return $this->cache(array(__METHOD__, $this->Session->read('Forum.roles')), function($self) {
			$keyPath = '{n}.Forum.id';
			$valuePath = array('%s%s', '{n}.tree_prefix', '{n}.Forum.title');
			$results = $self->filterByRole($self->find('all', array(
				'conditions' => array(
					'Forum.status' => Forum::OPEN,
					'Forum.accessRead' => Forum::YES,
					'OR' => array(
						'Forum.parent_id' => null,
						'Parent.status' => Forum::OPEN
					)
				),
				'contain' => array('Parent'),
				'order' => array('Forum.lft' => 'ASC')
			)));

			// Reorganize tree
			$stack = array();

			foreach ($results as $i => $result) {
				$count = count($stack);

				while ($stack && ($stack[$count - 1] < $result['Forum']['rght'])) {
					array_pop($stack);
					$count--;
				}

				$results[$i]['tree_prefix'] = str_repeat(' -- ', $count);
				$stack[] = $result['Forum']['rght'];
			}

			if (!$results) {
				return array();
			}

			// Reorganize the tree so top level forums are an optgroup
			$tree = Hash::combine($results, $keyPath, $valuePath);
			$hierarchy = array();
			$parent = null;

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
		});
	}

	/**
	 * Get the list of forums for the board index.
	 *
	 * @return array
	 */
	public function getIndex() {
		return $this->filterByRole($this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'conditions' => array(
				'Forum.parent_id' => null,
				'Forum.status' => self::OPEN,
				'Forum.accessRead' => self::YES
			),
			'contain' => array(
				'Children' => array(
					'conditions' => array(
						'Children.status' => self::OPEN,
						'Children.accessRead' => self::YES
					),
					'SubForum' => array(
						'fields' => array('SubForum.id', 'SubForum.aro_id', 'SubForum.title', 'SubForum.slug'),
						'conditions' => array(
							'SubForum.status' => self::OPEN,
							'SubForum.accessRead' => self::YES
						)
					),
					'LastTopic', 'LastPost', 'LastUser'
				)
			),
			'cache' => array(__METHOD__, $this->Session->read('Forum.roles'))
		)));
	}

	/**
	 * Filter down the forums if the user doesn't have the specific ARO (role) access.
	 *
	 * @param array $forums
	 * @return array
	 */
	public function filterByRole($forums) {
		$roles = (array) $this->Session->read('Forum.roles');
		$isAdmin = $this->Session->read('Forum.isAdmin');
		$isSuper = $this->Session->read('Forum.isSuper');

		if (!$roles) {
			return $forums;
		}

		foreach ($forums as $i => $forum) {
			$aro_id = null;

			if (isset($forum['Forum']['aro_id'])) {
				$aro_id = $forum['Forum']['aro_id'];
			} else if (isset($forum['aro_id'])) {
				$aro_id = $forum['aro_id'];
			}

			// Filter down children
			if (!empty($forum['Children'])) {
				$forums[$i]['Children'] = $this->filterByRole($forum['Children']);

			} else if (!empty($forum['SubForum'])) {
				$forums[$i]['SubForum'] = $this->filterByRole($forum['SubForum']);
			}

			// Admins and super mods get full access
			if ($isAdmin || $isSuper) {
				continue;
			}

			// Remove the forum if not enough role access
			if ($aro_id && !in_array($aro_id, $roles)) {
				unset($forums[$i]);
			}
		}

		return $forums;
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
