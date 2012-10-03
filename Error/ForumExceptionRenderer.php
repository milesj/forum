<?php

App::uses('ExceptionRenderer', 'Error');

class ForumExceptionRenderer extends ExceptionRenderer {

	/**
	 * Get the controller instance to handle the exception.
	 * Override this method in subclasses to customize the controller used.
	 * This method returns the built in `CakeErrorController` normally, or if an error is repeated
	 * a bare controller will be used.
	 *
	 * @param Exception $exception The exception to get a controller for.
	 * @return Controller
	 */
	protected function _getController($exception) {
		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}

		$response = new CakeResponse(array('charset' => Configure::read('App.encoding')));

		$controller = new ForumAppController($request, $response);
		$controller->viewPath = 'Errors';
		$controller->constructClasses();
		$controller->startupProcess();

		return $controller;
	}

}