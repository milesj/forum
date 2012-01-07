<?php
/** 
 * Ajax Handler Component
 *
 * A CakePHP Component that will automatically handle and render AJAX calls and apply the appropriate returned format and headers.
 *
 * @author      Miles Johnson - http://milesj.me
 * @copyright   Copyright 2006-2011, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/cakephp/ajax-handler
 */

App::import('Vendor', 'Forum.TypeConverter', array(
	'file' => 'TypeConverter.php'
));

class AjaxHandlerComponent extends Component {

	/**
	 * Current version.
	 *
	 * @access public
	 * @var string
	 */
	public $version = '2.0';
	
	/**
	 * Components.
	 *
	 * @access public
	 * @var array
	 */
	public $components = array('RequestHandler');

	/**
	 * Should we allow remote AJAX calls.
	 *
	 * @access public
	 * @var boolean
	 */
	public $allowRemote = false;

	/**
	 * A user given code associated with failure / success messages.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_code;

	/**
	 * Contains the success messages / errors.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_data;

	/**
	 * Which actions are handled as AJAX.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_handled = array();

	/**
	 * Determines if the AJAX call was a success or failure.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $_success = false;

	/**
	 * Load the Controller object.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function initialize($controller) {
		if ($controller->request->is('ajax')) {
			Configure::write('debug', 0);

			// Must disable security component for AJAX
			if (isset($controller->Security)) {
				$controller->Security->validatePost = false;
				$Controller->Security->csrfCheck = false;
			}

			// If not from this domain, destroy
			if (($this->allowRemote === false) && (strpos(env('HTTP_REFERER'), trim(env('HTTP_HOST'), '/')) === false)) {
				if (isset($controller->Security)) {
					$controller->Security->blackHole($controller, 'Invalid referrer detected for this request.');
				} else {
					$controller->redirect(null, 403, true);
				}
			}
		}

		$this->controller = $controller;
	}

	/**
	 * Determine if the action is an AJAX action and handle it.
	 *
	 * @access public
	 * @param Controller $controller
	 * @return void
	 */
	public function startup($controller) {
		$handled = ($this->_handled === array('*') || in_array($controller->action, $this->_handled));

		if ($controller->request->is('ajax') && !$handled) {
			if (isset($controller->Security)) {
				$controller->Security->blackHole($controller, 'You are not authorized to process this request.');
			} else {
				$controller->redirect(null, 401, true);
			}
		}

		$this->controller = $controller;
	}

	/**
	 * A list of actions that are handled as an AJAX call.
	 *
	 * @access public
	 * @return void
	 */
	public function handle() {
		$actions = func_get_args();
		
		if ($actions === array('*') || empty($actions)) {
			$this->_handled = array('*');
		} else {
			$this->_handled = array_unique(array_intersect($actions, get_class_methods($this->controller)));
		}
	}

	/**
	 * Respond the AJAX call with the gathered data.
	 *
	 * @access public
	 * @param string $type
	 * @param array $response
	 * @return mixed
	 */
	public function respond($type = 'json', array $response = array()) {
		if (!empty($response)) {
			$response = $response + array(
				'success' => false,
				'data' => '',
				'code' => null
			);

			$this->response($response['success'], $response['data'], $response['code']);
		}

		if ($type == 'html') {
			$this->RequestHandler->renderAs($this->controller, 'ajax');

		} else {
			$this->RequestHandler->respondAs($type);
			$this->controller->autoLayout = false;
			$this->controller->autoRender = false;

			echo $this->__format($type);
		}
	}

	/**
	 * Handle the response as a success or failure alongside a message or error.
	 *
	 * @access public
	 * @param boolean $success
	 * @param mixed $data
	 * @param mixed $code
	 * @return void
	 */
	public function response($success, $data = '', $code = null) {
		$this->_success = (bool) $success;
		$this->_data = $data;
		$this->_code = $code;
	}
	
	/**
	 * What should happen if the class is called stand alone.
	 *
	 * @access public
	 * @return mixed
	 */
	public function __toString() {
		return $this->respond();
	}

	/**
	 * Format the response into the right content type.
	 *
	 * @access private
	 * @param string $type
	 * @return mixed
	 */
	private function __format($type) {
		$response = array(
			'success' => $this->_success,
			'data' => $this->_data
		);

		if (!empty($this->_code)) {
			$response['code'] = $this->_code;
		}

		switch (strtolower($type)) {
			case 'json':
				$format = TypeConverter::toJson($response);
			break;
			case 'xml':
				$format = TypeConverter::toXml($response);
			break;
			case 'html';
			case 'text':
			default:
				$format = (string) $this->_data;
			break;
		}

		return $format;
	}

}
