<?php
/**
 * @copyright	Copyright 2006-2013, Miles Johnson - http://milesj.me
 * @license		http://opensource.org/licenses/mit-license.php - Licensed under the MIT License
 * @link		http://milesj.me/code/cakephp/forum
 */

App::uses('ForumAppModel', 'Forum.Model');

/**
 * @property User $User
 * @property Post $Post
 * @property Topic $Topic
 */
class PostRating extends ForumAppModel {

	const UP = 1;
	const DOWN = 0;

	/**
	 * Belongs to.
	 *
	 * @type array
	 */
	public $belongsTo = array(
		'User' => array(
			'className' => USER_MODEL
		),
		'Post' => array(
			'className' => 'Forum.Post'
		),
		'Topic' => array(
			'className' => 'Forum.Topic'
		)
	);

	/**
	 * Admin settings.
	 *
	 * @type array
	 */
	public $admin = array(
		'iconClass' => 'icon-star-half-empty'
	);

	/**
	 * Enum.
	 *
	 * @type array
	 */
	public $enum = array(
		'type' => array(
			self::UP => 'UP',
			self::DOWN => 'DOWN'
		)
	);

	/**
	 * Get all the rated posts within a topic.
	 *
	 * @param int $user_id
	 * @param int $topic_id
	 * @return array
	 */
	public function getRatingsInTopic($user_id, $topic_id) {
		return $this->find('list', array(
			'conditions' => array(
				'PostRating.user_id' => $user_id,
				'PostRating.topic_id' => $topic_id
			),
			'fields' => array('PostRating.id', 'PostRating.post_id')
		));
	}

	/**
	 * Check if the user has rated a post.
	 *
	 * @param int $user_id
	 * @param int $post_id
	 * @return bool
	 */
	public function hasRated($user_id, $post_id) {
		return (bool) $this->find('count', array(
			'conditions' => array(
				'PostRating.user_id' => $user_id,
				'PostRating.post_id' => $post_id
			)
		));
	}

	/**
	 * Rate a post.
	 *
	 * @param int $user_id
	 * @param int $post_id
	 * @param int $topic_id
	 * @param int $type
	 * @return bool
	 */
	public function ratePost($user_id, $post_id, $topic_id, $type) {
		$this->create();

		if ($this->save(array(
			'user_id' => $user_id,
			'post_id' => $post_id,
			'topic_id' => $topic_id,
			'type' => $type
		), false)) {
			if ($type == self::UP) {
				$this->Post->rateUp($post_id);
			} else {
				$this->Post->rateDown($post_id);
			}

			return true;
		}

		return false;
	}

}