<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property Forum $Forum
 * @property Topic $Topic
 * @property User $User
 * @property PostRating $PostRating
 */
class Post extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @type array
	 */
	public $belongsTo = array(
		'Forum' => array(
			'className' => 'Forum.Forum',
			'counterCache' => true
		),
		'Topic' => array(
			'className' => 'Forum.Topic',
			'counterCache' => true
		),
		'User' => array(
			'className' => USER_MODEL,
			'counterCache' => true
		)
	);

	/**
	 * Has many.
	 *
	 * @type array
	 */
	public $hasMany = array(
		'PostRating' => array(
			'className' => 'Forum.PostRating',
			'dependent' => true,
			'limit' => 25
		)
	);

	/**
	 * Validation.
	 *
	 * @type array
	 */
	public $validations = array(
		'default' => array(
			'forum_id' => array(
				'rule' => 'notEmpty'
			),
			'topic_id' => array(
				'rule' => 'notEmpty'
			),
			'user_id' => array(
				'rule' => 'notEmpty'
			),
			'content' => array(
				'rule' => 'notEmpty'
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @type array
	 */
	public $admin = array(
		'iconClass' => 'icon-comments',
		'editorFields' => array('content')
	);

	/**
	 * Validate and add a post.
	 *
	 * @param array $data
	 * @return bool|int
	 */
	public function addPost($data) {
		$this->set($data);

		if ($this->validates()) {
			$settings = Configure::read('Forum.settings');
			$isAdmin = $this->Session->read('Acl.isAdmin');

			if (($secondsLeft = $this->checkFlooding($settings['postFloodInterval'])) > 0 && !$isAdmin) {
				return $this->invalid('content', 'You must wait %s more second(s) till you can post a reply', $secondsLeft);

			} else if ($this->checkHourly($settings['postsPerHour']) && !$isAdmin) {
				return $this->invalid('content', 'You are only allowed to post %s time(s) per hour', $settings['postsPerHour']);

			} else {
				$this->create();
				$this->save($data, false, array('topic_id', 'forum_id', 'user_id', 'userIP', 'content'));

				$data['post_id'] = $this->id;

				$this->Topic->update($data['topic_id'], array(
					'lastPost_id' => $data['post_id'],
					'lastUser_id' => $data['user_id'],
				));

				$this->Topic->Forum->chainUpdate($data['forum_id'], array(
					'lastTopic_id' => $data['topic_id'],
					'lastPost_id' => $data['post_id'],
					'lastUser_id' => $data['user_id']
				));

				return $data['post_id'];
			}
		}

		return false;
	}

	/**
	 * Save the first post with a topic.
	 *
	 * @param array $data
	 * @return int
	 */
	public function addFirstPost($data) {
		$this->create();

		$this->save(array(
			'topic_id' => $data['topic_id'],
			'forum_id' => $data['forum_id'],
			'user_id' => $data['user_id'],
			'userIP' => $data['userIP'],
			'content' => $data['content']
		), false);

		return $this->id;
	}

	/**
	 * Check the posting flood interval.
	 *
	 * @param int $interval
	 * @return bool
	 */
	public function checkFlooding($interval) {
		if ($posts = $this->Session->read('Forum.posts')) {
			$timeLeft = time() - array_pop($posts);

			if ($timeLeft <= $interval) {
				return $interval - $timeLeft;
			}
		}

		return false;
	}

	/**
	 * Check the hourly posting.
	 *
	 * @param int $max
	 * @return bool
	 */
	public function checkHourly($max) {
		$pastHour = strtotime('-1 hour');

		if ($posts = $this->Session->read('Forum.posts')) {
			$count = 0;

			foreach ($posts as $time) {
				if ($time >= $pastHour) {
					++$count;
				}
			}

			if ($count >= $max) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Return a post based on ID.
	 *
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		return $this->find('first', array(
			'conditions' => array('Post.id' => $id),
			'contain' => array(
				'Topic', 'User',
				'Forum' => array('Parent')
			),
			'cache' => array(__METHOD__, $id)
		));
	}

	/**
	 * Return a list of IDs within a topic.
	 *
	 * @param int $topic_id
	 * @return array
	 */
	public function getIdsForTopic($topic_id) {
		return $this->find('list', array(
			'conditions' => array('Post.topic_id' => $topic_id),
			'order' => array('Post.id' => 'ASC')
		));
	}

	/**
	 * Return the latest posts by a user.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getLatestByUser($user_id, $limit = 5) {
		return $this->find('all', array(
			'conditions' => array('Post.user_id' => $user_id),
			'order' => array('Post.created' => 'DESC'),
			'limit' => $limit,
			'contain' => array(
				'Topic' => array('User')
			)
		));
	}

	/**
	 * Return the latest posts by a user, grouped by the topic ID.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getGroupedLatestByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.user_id' => $user_id),
			'order' => array('Post.created' => 'DESC'),
			'group' => array('Post.topic_id'),
			'limit' => $limit,
			'contain' => array(
				'Topic' => array('LastUser', 'LastPost', 'User')
			),
			'cache' => array(__METHOD__, $user_id, $limit),
			'cacheExpires' => '+5 minutes'
		));
	}

	/**
	 * Get popular scoring posts by a user.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getPopularByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.user_id' => $user_id),
			'order' => array('Post.score' => 'DESC'),
			'limit' => $limit,
			'cache' => array(__METHOD__, $user_id, $limit),
			'cacheExpires' => '+5 minutes'
		));
	}

	/**
	 * Get popular scoring posts in a topic.
	 *
	 * @param int $topic_id
	 * @param int $limit
	 * @return array
	 */
	public function getPopularInTopic($topic_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.topic_id' => $topic_id),
			'order' => array('Post.score' => 'DESC'),
			'limit' => $limit,
			'contain' => array('User'),
			'cache' => array(__METHOD__, $topic_id, $limit),
			'cacheExpires' => '+5 minutes'
		));
	}

	/**
	 * Return a post for quoting.
	 *
	 * @param int $id
	 * @return array
	 */
	public function getQuote($id) {
		return $this->find('first', array(
			'conditions' => array('Post.id' => $id),
			'contain' => array('User')
		));
	}

	/**
	 * Return the latest posts in a topic.
	 *
	 * @param int $topic_id
	 * @param int $limit
	 * @return array
	 */
	public function getTopicReview($topic_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array('Post.topic_id' => $topic_id),
			'contain' => array('User'),
			'order' => array('Post.created' => 'DESC'),
			'limit' => $limit
		));
	}

	/**
	 * Increase the down ratings.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function rateDown($id) {
		$down = (int) Configure::read('Forum.settings.rateDownPoints');

		return $this->updateAll(
			array('Post.down' => 'Post.down + ' . $down, 'Post.score' => 'Post.score - ' . $down),
			array('Post.id' => $id)
		);
	}

	/**
	 * Increase the up ratings.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function rateUp($id) {
		$up = (int) Configure::read('Forum.settings.rateUpPoints');

		return $this->updateAll(
			array('Post.up' => 'Post.up + ' . $up, 'Post.score' => 'Post.score + ' . $up),
			array('Post.id' => $id)
		);
	}

	/**
	 * Move all posts to a new forum.
	 *
	 * @param int $start_id
	 * @param int $moved_id
	 * @return bool
	 */
	public function moveAll($start_id, $moved_id) {
		return $this->updateAll(
			array('Post.forum_id' => $moved_id),
			array('Post.forum_id' => $start_id)
		);
	}

	/**
	 * Parse the HTML version.
	 *
	 * @param array $options
	 * @return bool
	 */
	public function beforeSave($options = array()) {
		return $this->validateDecoda('Post');
	}

	/**
	 * Null associations.
	 */
	public function afterDelete() {
		$this->Forum->updateAll(
			array('Forum.lastPost_id' => null),
			array('Forum.lastPost_id' => $this->id)
		);

		$this->Topic->updateAll(
			array('Topic.firstPost_id' => null),
			array('Topic.firstPost_id' => $this->id)
		);

		$this->Topic->updateAll(
			array('Topic.lastPost_id' => null),
			array('Topic.lastPost_id' => $this->id)
		);
	}

}
