<?php
/**
 * Forum - SubscriptionShell
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/forum
 */

Configure::write('debug', 2);
Configure::write('Cache.disable', true);

App::uses('CakeEmail', 'Network/Email');

class SubscriptionShell extends Shell {

	/**
	 * Models.
	 *
	 * @access public
	 * @var array
	 */
	public $uses = array('Forum.Subscription');

	/**
	 * Execute!
	 */
	public function main() {
		$this->config = Configure::read('Forum');
		$this->settings = ClassRegistry::init('Forum.Setting')->getSettings();
		$this->timeframe = '-' . (isset($this->params['timeframe']) ? $this->params['timeframe'] : '24 hours');

		// Begin
		$this->out();
		$this->out('Plugin: Forum');
		$this->out('Version: ' . $this->config['version']);
		$this->out('Copyright: Miles Johnson, 2010-' . date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');
		$this->out('Shell: Subscription');
		$this->out();
		$this->out('Queries the database for the latest activity within subscribed topics and forums, then notifies the subscribers with the updates.');
		$this->hr(1);

		// Gather and organize subscriptions
		$topicIds = array();
		$forumIds = array();
		$users = array();
		$count = 0;

		$results = $this->Subscription->find('all', array(
			'contain' => array('User')
		));

		if (!$results) {
			$this->out('No subscriptions to send...');
			return;
		}

		foreach ($results as $result) {
			$user_id = $result['Subscription']['user_id'];
			$topic_id = $result['Subscription']['topic_id'];
			$forum_id = $result['Subscription']['forum_id'];

			if (empty($users[$user_id])) {
				$users[$user_id] = $result['User'];
				$users[$user_id]['topics'] = array();
				$users[$user_id]['forums'] = array();
			}

			if ($topic_id) {
				$users[$user_id]['topics'][] = $topic_id;
				$topicIds[] = $topic_id;
			}

			if ($forum_id) {
				$users[$user_id]['forums'][] = $forum_id;
				$forumIds[] = $forum_id;
			}
		}

		// Query for the latest topics
		$topics = $this->getTopics($forumIds, $topicIds);

		if (!$topics) {
			$this->out('No new activity...');
			return;
		}

		$email = new CakeEmail();
		//$email->transport('Debug');
		$email->subject(sprintf(__d('forum', '%s [Subscriptions]'), $this->settings['site_name']));
		$email->from($this->settings['site_email']);
		$email->replyTo($this->settings['site_email']);
		$email->emailFormat('text');

		// Loop over each user and send one email
		foreach ($users as $user_id => $user) {
			$email->to($user[$this->config['userMap']['email']]);

			if ($message = $this->formatEmail($user, $topics)) {
				$this->out(sprintf('... %s', $user[$this->config['userMap']['username']]));

				$count++;
			}
		}

		$this->hr(1);
		$this->out(sprintf('Notified %d user(s)', $count));
	}

	/**
	 * Return all topics with new activity found within specific forums or by a specific ID.
	 * Organize the topics array by ID before returning.
	 *
	 * @access public
	 * @param array $forumIds
	 * @param array $topicIds
	 * @return array
	 */
	public function getTopics(array $forumIds, array $topicIds) {
		$clean = array();
		$timestamp = date('Y-m-d H:i:s', strtotime($this->timeframe));

		// Get topics based on forum IDs
		$results = $this->Subscription->Topic->find('all', array(
			'conditions' => array(
				'Topic.forum_id' => $forumIds,
				'Topic.created >=' => $timestamp
			),
			'contain' => array('Forum')
		));

		if ($results) {
			foreach ($results as &$result) {
				$clean[$result['Topic']['id']] = $result;
			}
		}

		// Get topics based on ID
		$results = $this->Subscription->Topic->find('all', array(
			'conditions' => array(
				'Topic.id' => $topicIds,
				'Topic.modified >=' => $timestamp
			),
			'contain' => array('Forum')
		));

		if ($results) {
			foreach ($results as &$result) {
				$result['Topic']['post_count_new'] = $this->Subscription->Topic->Post->find('count', array(
					'conditions' => array(
						'Post.topic_id' => $result['Topic']['id'],
						'Post.created >=' => $timestamp
					)
				));

				$clean[$result['Topic']['id']] = $result;
			}
		}

		return $clean;
	}

	/**
	 * Format the email by looping over all topics.
	 *
	 * @access public
	 * @param array $user
	 * @param array $topics
	 * @return string
	 */
	public function formatEmail(array $user, array $topics) {
		$divider = "\n\n------------------------------\n\n";
		$count = 0;
		$url = trim($this->settings['site_main_url'], '/');

		$message  = sprintf(__d('forum', 'Hello %s,'), $user[$this->config['userMap']['username']]) . "\n\n";
		$message .= sprintf(__d('forum', 'You have asked to be notified for any new activity within %s. Below you will find an update on all your forum subscriptions. The last subscription update was sent on %s.'), $this->settings['site_name'], date('m/d/Y h:ia', strtotime($this->timeframe))) . "\n\n";
		$message .= __d('forum', 'You may unsubscribe from a forum or topic by clicking the "Unsubscribe" button found within the respective forum or topic.');

		// Show forum topics first
		if (!empty($user['forums'])) {
			$message .= $divider;
			$message .= __d('forum', 'Forum Subscriptions');
			$message .= $divider;
			$message .= __d('forum', 'The following topics have been created.') . "\n\n";

			foreach ($topics as $topic) {
				if (!in_array($topic['Topic']['forum_id'], $user['forums'])) {
					continue;
				}

				$message .= sprintf(__d('forum', '%s [%s] %s'),
					$topic['Topic']['title'],
					$topic['Forum']['title'],
					date('m/d/Y', strtotime($topic['Topic']['created']))) . "\n";

				$message .= $url . '/forum/topics/view/' . $topic['Topic']['slug'] . "\n\n";

				$count++;
			}
		}

		// Show updated topics last
		if (!empty($user['topics'])) {
			$message .= $divider;
			$message .= __d('forum', 'Topic Subscriptions');
			$message .= $divider;
			$message .= __d('forum', 'The following topics have seen recent post activity.') . "\n\n";

			foreach ($topics as $topic) {
				if (!in_array($topic['Topic']['id'], $user['topics'])) {
					continue;
				}

				$message .= sprintf(__d('forum', '%s [%s] %d new posts'),
					$topic['Topic']['title'],
					$topic['Forum']['title'],
					$topic['Topic']['post_count_new']) . "\n";

				$message .= $url . '/forum/topics/view/' . $topic['Topic']['slug'] . "\n\n";

				$count++;
			}
		}

		// Fail if there are no updates
		if ($count == 0) {
			return false;
		}

		$message .= $divider;
		$message .= $this->settings['site_name'] . "\n";
		$message .= $url;

		return $message;
	}

}

