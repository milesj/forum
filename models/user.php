<?php
/** 
 * user.php
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @package		Cupcake - User Model
 * @link		www.milesj.me/resources/script/forum-plugin
 * @link		www.milesj.me/forum
 */
 
class User extends ForumAppModel {

	/**
	 * Has many
	 * @access public
	 * @var array
	 */
	public $hasMany = array(
		'Topic' => array(
			'className'	=> 'Forum.Topic',
			'dependent' => true,
			'exclusive' => true
		),
		'Post' => array(
			'className'	=> 'Forum.Post',
			'dependent' => true,
			'exclusive' => true
		),
		'Moderator' => array(
			'className'	=> 'Forum.Moderator',
			'dependent' => true,
			'exclusive' => true
		),
		'Access' => array(
			'className'	=> 'Forum.Access',
			'dependent' => true,
			'exclusive' => true
		)
	);
	
	/**
	 * Validation
	 * @access public
	 * @var array
	 */ 
	public $validate = array(
		'username' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That username has already been taken',
				'on' => 'create'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a username'
			)
		),
		'password' => array(
			'rule' => 'notEmpty',
			'message' => 'Please enter a password'
		),
		'oldPassword' => array(
			'rule' => array('isPassword'),
			'message' => 'The old password did not match'
		),
		'newPassword' => array(
			'isMatch' => array(
				'rule' => array('isMatch', 'confirmPassword'),
				'message' => 'The passwords did not match'
			),
			'custom' => array(
				'rule' => array('custom', '/^[-_a-zA-Z0-9]+$/'),
				'message' => 'Your password may only be alphanumeric'
			),
			'between' => array(
				'rule' => array('between', 6, 20),
				'message' => 'Your password must be 6-20 characters in length'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a password'
			)
		),
		'email' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That email has already been taken',
				'on' => 'create'
			),
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Your email is invalid'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Your email is required'
			)
		)
	);
	
	/**
	 * Retrieve and reset information for a forgotten password
	 * @access public
	 * @param array $data
	 * @return array
	 */
	public function forgotRetrieval($data) {
		$user = $this->find('first', array(
			'conditions' => array(
				'OR' => array(
					array('User.email' => $data['User']['email']),
					array('User.username' => $data['User']['username'])	
				)
			)
		));
		
		if (empty($user)) {
			$this->invalidate('username', 'No user was found with either of those credentials');
			return false;
		}
		
		return $user;
	}
	
	/**
	 * Generates a string of random characters
	 * @access public
	 * @param int $length
	 * @return string
	 */
	public function generate($length = 10) {
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$return = '';
		
		if ($length > 0) {
			$totalChars = mb_strlen($characters) - 1;
			for ($i = 0; $i <= $length; ++$i) {
				$return .= $characters[rand(0, $totalChars)];
			}
		}
		
		return $return;
	}
	
	/**
	 * Get a users profile: info, access levels, moderations
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getProfile($id) {
		return $this->find('first', array(
			'conditions' => array('User.id' => $id),	
			'contain' => array(
				'Access' => array('AccessLevel'),
				'Moderator' => array('ForumCategory')
			)
		));
	}
	
	/**
	 * Get the latest users signed up
	 * @access public
	 * @param $limit
	 * @return array
	 */
	public function getLatest($limit = 10) {
		return $this->find('all', array(
			'limit' => $limit,
			'order' => 'User.created DESC'
		));
	}

	/**
	 * Get the newest signup
	 * @access public
	 * @return array
	 */
	public function getNewestUser() {
		return $this->find('first', array(
			'fields' => array('User.id', 'User.username'),
			'order' => 'User.created DESC',
			'limit' => 1
		));
	}
	
	/**
	 * Increase the post count
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increasePosts($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."users` AS `User` SET `User`.`totalPosts` = `User`.`totalPosts` + 1 WHERE `User`.`id` = $id");
	}
	
	/**
	 * Increase the topic count
	 * @access public
	 * @param int $id
	 * @return boolean
	 */
	public function increaseTopics($id) {
		return $this->query("UPDATE `". $this->tablePrefix ."users` AS `User` SET `User`.`totalTopics` = `User`.`totalTopics` + 1 WHERE `User`.`id` = $id");
	}
	
	/**
	 * Checks to see if the old password matches their input
	 * @access public
	 * @param array $data
	 * @return boolean
	 */
	public function isPassword($data) {
		$user = $this->find('first', array(
			'conditions' => array('User.id' => $_SESSION['Auth']['User']['id']),
			'fields' => array('User.password'),
			'contain' => false
		));
		
		$data = array_values($data);
		$var1 = Security::hash($data[0], null, true);
		$var2 = $user['User']['password'];
		
		return ($var1 === $var2);
	}	
	
	/**
	 * Login the user and update records
	 * @access public
	 * @param array $user
	 * @return boolean
	 */
	public function login($user) {
		if (!empty($user)) {
			$data = array(
				'currentLogin' => date('Y-m-d H:i:s'),
				'lastLogin' => $user['User']['currentLogin']
			);
			
			$this->id = $user['User']['id'];
			return $this->save($data, false, array_keys($data));
		}
		
		return true;
	}
	
	/**
	 * Change the users password
	 * @access public
	 * @param int $id
	 * @param string $password
	 * @return boolean
	 */
	public function resetPassword($id, $password) {
		$this->id = $id;
		return $this->saveField('password', $password);
	}
	
	/**
	 * Get whos online within the past x minutes
	 * @access public
	 * @param int $minutes
	 * @return array
	 */
	public function whosOnline($minutes) {
		$past = date('Y-m-d H:i:s', strtotime('-'. $minutes .' minutes'));
		
		return $this->find('all', array(
			'conditions' => array('User.currentLogin >' => $past),
			'fields' => array('User.id', 'User.username'),
			'contain' => false
		));
	}
	
	/**
	 * Extra validation checking
	 * @access public
	 * @return boolean
	 */
	public function beforeValidate() {
		$action = (isset($this->action)) ? $this->action : null;
		
		if ($action == 'login') {
			unset($this->validate['username']['isUnique']);
			
		} else if ($action == 'signup') {
			$this->validate['security'] = array(
				'equalTo' => array(
					'rule' => array('equalTo', ForumConfig::getInstance()->settings['security_answer']),
					'message' => 'Your security answer is incorrect, please try again!'
				),
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'The security answer is required to proceed'
				),
				'required' => true
			);
		}
		
		return true;
	}
	
}
