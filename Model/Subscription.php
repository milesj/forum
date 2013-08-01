<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property User $User
 * @property Forum $Forum
 * @property Topic $Topic
 */
class Subscription extends ForumAppModel {

	/**
	 * Belongs to.
	 *
	 * @type array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => USER_MODEL
		),
		'Forum' => array(
			'className' => 'Forum.Forum',
			'foreignKey' => 'forum_id'
		),
		'Topic' => array(
			'className' => 'Forum.Topic',
			'foreignKey' => 'topic_id'
		)
	);

	/**
	 * Validation.
	 *
	 * @type array
	 */
	public $validations = array(
		'default' => array(
			'user_id' => array(
				'rule' => 'notEmpty',
			)
		)
	);

	/**
	 * Admin settings.
	 *
	 * @type array
	 */
	public $admin = array(
		'iconClass' => 'icon-pushpin'
	);

	/**
	 * Get all subscribed forums from a user.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getForumSubscriptionsByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array(
				'Subscription.user_id' => $user_id,
				'Subscription.forum_id !=' => null
			),
			'contain' => array('Forum'),
			'limit' => $limit
		));
	}

	/**
	 * Get all subscribed topics from a user.
	 *
	 * @param int $user_id
	 * @param int $limit
	 * @return array
	 */
	public function getTopicSubscriptionsByUser($user_id, $limit = 10) {
		return $this->find('all', array(
			'conditions' => array(
				'Subscription.user_id' => $user_id,
				'Subscription.topic_id !=' => null
			),
			'contain' => array(
				'Topic' => array('LastPost', 'LastUser', 'User')
			),
			'limit' => $limit
		));
	}

	/**
	 * Determine if the user is already subscribed to a forum.
	 *
	 * @param int $user_id
	 * @param int $forum_id
	 * @return int
	 */
	public function isSubscribedToForum($user_id, $forum_id) {
		return $this->find('first', array(
			'conditions' => array(
				'Subscription.user_id' => $user_id,
				'Subscription.forum_id' => $forum_id
			)
		));
	}

	/**
	 * Determine if the user is already subscribed to a topic.
	 *
	 * @param int $user_id
	 * @param int $topic_id
	 * @return int
	 */
	public function isSubscribedToTopic($user_id, $topic_id) {
		return $this->find('first', array(
			'conditions' => array(
				'Subscription.user_id' => $user_id,
				'Subscription.topic_id' => $topic_id
			)
		));
	}

	/**
	 * Subscribe a user to a forum.
	 *
	 * @param int $user_id
	 * @param int $forum_id
	 * @return bool
	 */
	public function subscribeToForum($user_id, $forum_id) {
		$forum = $this->Forum->getById($forum_id);

		if (!$forum || $this->isSubscribedToForum($user_id, $forum_id)) {
			return false;
		}

		$this->create();

		return $this->save(array(
			'user_id' => $user_id,
			'forum_id' => $forum_id
		), false);
	}

	/**
	 * Subscribe a user to a topic.
	 *
	 * @param int $user_id
	 * @param int $topic_id
	 * @return bool
	 */
	public function subscribeToTopic($user_id, $topic_id) {
		$topic = $this->Topic->getById($topic_id);

		if (!$topic || $this->isSubscribedToTopic($user_id, $topic_id)) {
			return false;
		}

		$this->create();

		return $this->save(array(
			'user_id' => $user_id,
			'topic_id' => $topic_id
		), false);
	}

	/**
	 * Unsubscribe a user subscription.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function unsubscribe($id) {
		return $this->delete($id, true);
	}

}