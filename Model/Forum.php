<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property Forum $Parent
 * @property Forum $Children
 * @property Topic $Topic
 * @property Topic $LastTopic
 * @property Post $LastPost
 * @property User $LastUser
 * @property Moderator $Moderator
 * @property Subscription $Subscription
 * @property RequestObject $ReadTopicAccess
 * @property RequestObject $CreateTopicAccess
 * @property RequestObject $CreatePollAccess
 * @property RequestObject $CreatePostAccess
 */
class Forum extends ForumAppModel {

	/**
	 * Behaviors.
	 *
	 * @type array
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
	 * @type array
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
		'ReadTopicAccess' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'accessRead'
		),
		'CreateTopicAccess' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'accessPost'
		),
		'CreatePollAccess' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'accessPoll'
		),
		'CreatePostAccess' => array(
			'className' => 'Admin.RequestObject',
			'foreignKey' => 'accessReply'
		)
	);

	/**
	 * Has many.
	 *
	 * @type array
	 */
	public $hasMany = array(
		'Topic' => array(
			'className' => 'Forum.Topic',
			'limit' => 25,
			'order' => array('Topic.created' => 'DESC'),
			'dependent' => false,
		),
		'Children' => array(
			'className' => 'Forum.Forum',
			'foreignKey' => 'parent_id',
			'order' => array('Children.orderNo' => 'ASC'),
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
	 * @type array
	 */
	public $validations = array(
		'default' => array(
			'title' => array(
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
	 * @type array
	 */
	public $admin = array(
		'iconClass' => 'icon-list-alt',
		'paginate' => array(
			'order' => array('Forum.lft' => 'ASC')
		)
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
	 * Close a topic.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function close($id) {
		$this->id = $id;

		return $this->saveField('status', self::CLOSED);
	}

	/**
	 * Get a forum.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function getBySlug($slug) {
		$forum = $this->find('first', array(
			'conditions' => array(
				'Forum.slug' => $slug
			),
			'contain' => array(
				'Parent',
				'Children' => array(
					'conditions' => array(
						'Children.status' => self::OPEN
					),
					'LastTopic', 'LastPost', 'LastUser'
				),
				'Moderator' => array('User')
			),
			'cache' => array(__METHOD__, $slug)
		));

		return $this->filterByRole($forum);
	}

	/**
	 * Get the tree and reorganize into a hierarchy.
	 * Code borrowed from TreeBehavior::generateTreeList().
	 *
	 * @param bool $group
	 * @return array
	 */
	public function getHierarchy($group = true) {
		return $this->cache(array(__METHOD__, $this->Session->read('Acl.roles'), $group), function($self) use ($group) {
			/** @type Forum $self */

			$keyPath = '{n}.Forum.id';
			$valuePath = array('%s%s', '{n}.tree_prefix', '{n}.Forum.title');
			$results = $self->filterByRole($self->find('all', array(
				'conditions' => array(
					'Forum.status' => Forum::OPEN,
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

			$tree = Hash::combine($results, $keyPath, $valuePath);

			if (!$group) {
				return $tree;
			}

			// Reorganize the tree so top level forums are an optgroup
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
		$forums = $this->find('all', array(
			'order' => array('Forum.orderNo' => 'ASC'),
			'conditions' => array(
				'Forum.parent_id' => null,
				'Forum.status' => self::OPEN
			),
			'contain' => array(
				'Children' => array(
					'conditions' => array(
						'Children.status' => self::OPEN
					),
					'Children' => array(
						'fields' => array('Children.id', 'Children.accessRead', 'Children.title', 'Children.slug'),
						'conditions' => array(
							'Children.status' => self::OPEN
						)
					),
					'LastTopic', 'LastPost', 'LastUser'
				)
			),
			'cache' => array(__METHOD__, $this->Session->read('Acl.roles'))
		));

		return $this->filterByRole($forums);
	}

	/**
	 * Filter down the forums if the user doesn't have the specific ARO (role) access.
	 *
	 * @param array $forums
	 * @return array
	 */
	public function filterByRole($forums) {
		$roles = (array) $this->Session->read('Acl.roles');
		$isAdmin = $this->Session->read('Acl.isAdmin');
		$isSuper = $this->Session->read('Acl.isSuper');
		$isMulti = true;

		if (!isset($forums[0])) {
			$forums = array($forums);
			$isMulti = false;
		}

		foreach ($forums as $i => $forum) {
			$aro_id = null;

			if (isset($forum['Forum']['accessRead'])) {
				$aro_id = $forum['Forum']['accessRead'];
			} else if (isset($forum['accessRead'])) {
				$aro_id = $forum['accessRead'];
			}

			// Filter down children
			if (!empty($forum['Children'])) {
				$forums[$i]['Children'] = $this->filterByRole($forum['Children']);
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

		if (!$isMulti) {
			return $forums[0];
		}

		return array_values($forums);
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

	/**
	 * Open a topic.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function open($id) {
		$this->id = $id;

		return $this->saveField('status', self::OPEN);
	}

}
