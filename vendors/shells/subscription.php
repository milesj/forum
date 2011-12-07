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
Configure::load('Forum.config');

App::import('Core', 'Controller');
App::import('Component', 'Email');

class SubscriptionShell extends Shell {
	
	public $uses = array('Forum.Subscription');
	
	public function main() {
		$this->config = Configure::read('Forum');
		$this->settings = ClassRegistry::init('Forum.Setting')->getSettings();
		$this->timeframe = '-24 hours';

		// Begin
		$this->out();
		$this->out('Plugin: Forum');
		$this->out('Version: ' . $this->config['version']);
		$this->out('Copyright: Miles Johnson, 2010-'. date('Y'));
		$this->out('Help: http://milesj.me/code/cakephp/forum');
		$this->out('Shell: Subscription');
		$this->out();
		$this->out('Queries the database for the latest activity within subscribed topics and forums, then notifies the subscribers with the updates.');	

		// Gather
		$this->topics = array();
		$this->forums = array();
		$this->users = array();
		
		$results = $this->Subscription->find('all', array(
			'contain' => array('User')
		));
		
		if (empty($results)) {
			$this->out('No subscriptions to send.');
			return;
		}
		
		foreach ($results as $result) {
			$user_id = $result['Subscription']['user_id'];
			$topic_id = $result['Subscription']['topic_id'];
			$forum_id = $result['Subscription']['forum_id'];
			
			if ($topic_id) {
				if (isset($this->topics[$topic_id])) {
					$this->topics[$topic_id][] = $user_id;
				} else {
					$this->topics[$topic_id] = array($user_id);
				}
			}
			
			if ($forum_id) {
				if (isset($this->forums[$forum_id])) {
					$this->forums[$forum_id][] = $user_id;
				} else {
					$this->forums[$forum_id] = array($user_id);
				}
			}
			
			$this->users[$user_id] = $result['User'];
		}
		
		// Setup
		$this->controller =& new Controller();
		$this->email =& new EmailComponent(null);
		$this->email->initialize($this->controller);
		$this->email->to = $this->settings['site_email'];
		$this->email->from = $this->settings['site_email'];
		$this->email->replyTo = $this->settings['site_email'];
		$this->email->sendAs = 'text';
		
		// Notify
		$this->sendForumSubscriptions();
		$this->sendTopicSubscriptions();
	}
	
	public function sendForumSubscriptions() {
		$this->hr(1);
		$this->out('Sending forum subscriptions...');
		
		if (empty($this->forums)) {
			$this->out('No subscriptions.');
			return;
		}
		
		$forums = array();
		$results = $this->Subscription->Topic->find('all', array(
			'conditions' => array(
				'Topic.forum_id' => array_keys($this->forums),
				'Topic.created >=' => date('Y-m-d H:i:s', strtotime($this->timeframe))
			),
			'contain' => array('User', 'Forum')
		));
		
		if (empty($results)) {
			$this->out('No new activity.');
			return;
		}
		
		// Separate topics into their forums
		foreach ($results as $topic) {
			$forum_id = $topic['Topic']['forum_id'];
			
			if (empty($forums[$forum_id])) {
				$forums[$forum_id] = array();
			}
			
			$forums[$forum_id][] = $topic;
		}
		
		// Loop over each form and send email about the latest topics
		foreach ($forums as $forum_id => $topics) {
			$bcc = array();
			$forum = $topics[0]['Forum'];
			
			foreach ($this->forums[$forum_id] as $user_id) {
				$bcc[] = $this->users[$user_id][$this->config['userMap']['email']];
			}
			
			$this->controller->set('topics', $topics);
			$this->email->subject = sprintf(__d('forum', '%s [Forum Subscriptions] %s', true), $this->settings['site_name'], $forum['title']);
			$this->email->bcc = $bcc;
			$this->email->delivery = 'debug';
			echo $this->email->send($this->_formatForumEmail($forum, $topics));
			
			$this->out(sprintf('... %s (%d)', $forum['title'], count($bcc)));
		}
	}
	
	public function sendTopicSubscriptions() {
	}
	
	protected function _formatForumEmail($forum, $topics) {
		$message = '';
		
		foreach ($topics as $topic) {
			$message .= sprintf(__d('forum', '%s - %s [%d posts, %d views]', true), $topic['Topic']['title'], $topic['User'][$this->config['userMap']['username']], $topic['Topic']['post_count'], $topic['Topic']['view_count']) . "\n";
			$message .= trim($this->settings['site_main_url'], '/') . '/forum/topics/view/' . $topic['Topic']['slug'] . "\n\n";
		}
		
		return $message;
	}
	
}

