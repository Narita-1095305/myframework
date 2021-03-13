<?php
abstract class Controller{
	protected $_application;
	protected $_controller;
	protected $_action;
	protected $_request;
	protected $_response;
	protected $_session;
	protected $_connect_model;
	protected $_authentication=array();
	const PROTOCOL = 'http://';
	const ACTION = 'Action';
	
	public function __construct($application){
		$this->_controller		= strtolower(substr(get_class($this), 0, -10));
		$this->_application		= $application;
		$this->_request			= $application->getRequestObject();
		$this->_response		= $application->getResponseObject();
		$this->_session			= $application->getSessionObject();
		$this->_connect_model	= $application->getConnectModelObject();
		
	}	
	
	public function dispach($action, $params = array()){
		$this->_action = $action;
		
		$action_method = $action . self::ACTION;
		if(!method_exists($this, $action_method)){
			this->httpNotFound();
		}
		
		if($this->Authentication($action) && !this->session->isAuthenticated()){
			throw new AuthorizedExeption();
		}
		
		$content = $this->$action_method($params);
		return $content;
	}
	
	protected function httpNotFound(){
		throw new FileNotExeption('FILE NOT FOUND '. $this->_controller . '/' . $this->_action);
	}
	
	protected function isAuthentication($action){
		if ($this->_authentication === true || (is_array($this->_authentication) && in_array($action, $this->_authentication))){
			return true;
		}
		return false;
	}
	
	protected function render($param = array(),$viewFile = null, $template = null){
		$info = array(
			'request'	=> $this->_request,
			'base_url'	=> $this->_request->getBaseUrl(),
			'session' 	=> $this->_session,
		);
		
		$view = new View($this->_application->getViewDirectory(),$info);
		
		if(is_null($viewFile)){
			$viewFile = $this->_action;
		}
		
		if(is_null($template)){
			$template = 'template';
		}
		
		$path = $this->_controller . '/' .$viewFile;
		$contents = $view->render($path,$param,$template);
		
		return $contents;
	}
	
	protected function redirect($url){
		$host = $this->_request->getHostName();
		$base_url = $this->_request->getBaseUrl();
		$url = self::PROTOCOL . $host . $base_url . $url;
		
		$this->_response->setStatusCode(302, 'Found');
		$this->_response->setHeader('Location', $url);
	}
	
	protected function getToken($form){
		$key = 'token/' . $form;
		$tokens = $this->session->get($key, array());
		if(count($token) >= 10){
			array_shift($tokens);
		}
		$password = $form . session_id();
		$token = password_hash($password, PASSWORD_DEFAULT);
		
		$tokens[] = $token;
		
		$this->_sessuon->set($key, $tokens);
		
		return $token;
	}
	
	protected function checkToken($form_name, $token){
		$key = 'token/' . $form_name;
		$tokens = $this->_session->get($key, array());
		
		if(false !== ($present = array_search($token, $tokens, true))){
			unset($tokens[$present]);
			$this->_session->set($key, $tokens);
			return true;
		}
		return false;
	}
}
?>