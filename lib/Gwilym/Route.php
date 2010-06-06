<?php

class Gwilym_Route
{
	protected $_request;
	protected $_controller;
	protected $_args;

	/**
	* @param Gwilym_Request $request
	* @param string $controller
	* @param array $args
	* @return Gwilym_Route
	*/
	public function __construct (Gwilym_Request $request, $controller, $args = array())
	{
		$this->_request = $request;
		$this->_controller = $controller;
		$this->_args = $args;
	}

	/**
	* @return void
	*/
	public function follow ()
	{
		/** @var Gwilym_Controller */
		$controller = new $this->_controller($this->_request, $this->_args);

		if ($controller instanceof Gwilym_Controller_MethodSpecific) {
			$method = $this->_request->method();
			$response = $controller->response();

			if ($controller instanceof Gwilym_Controller_PostOnly && $method !== 'POST') {
				$response->status(Gwilym_Response::STATUS_METHOD_NOT_ALLOWED);
				$response->header('Allow', 'POST');
				$response->end();
			}

			if ($controller instanceof Gwilym_Controller_GetOnly && $method !== 'GET') {
				$response->status(Gwilym_Response::STATUS_METHOD_NOT_ALLOWED);
				$response->header('Allow', 'GET');
				$response->end();
			}
		}

		if ($controller->before() !== false)
		{
			$controller->action();
			$controller->after();
		}

		$controller->view()->display();
	}

	public function request ()
	{
		return $this->_request;
	}

	public function controller ()
	{
		return $this->_controller;
	}

	public function args ()
	{
		return $this->_args;
	}

	/**
	* Return a URI representing this route based on default router of the current request.
	*
	* @return string
	*/
	public function uri ()
	{
		return $this->request()->routeToUri($this);
	}
}
