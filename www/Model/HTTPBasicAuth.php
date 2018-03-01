<?php
	/*
		Authentification, using Http Basic Authentification

		Description of methods watch inside interface class
	*/

	require_once('IAuthorization.php');		// interface class

	class HTTPBasicAuth implements IAuthorization{

		private $username;

		private $password;

	    private $validator;

	    private $view;

		function __construct() {
			global $classes;
			// inject dependencies
			$this->view = $classes->get('View');
			$this->validator = $classes->get('Validator');
			// default username and password
			$this->username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
			$this->password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
		}

		public function check() {
			global $container;
			if (!isset($this->username) || empty($this->username)) {
				// username is not set
			    $this->view->sendUnauthorized();
			} else {
			    try {
			    	$this->validator->isValidUserName($this->username);
			    	$this->validator->isValidPassword($this->password);
			    } catch (Exception $e) {
			    	// username or password is not valid
					$this->view->sendUnauthorized($e->getMessage());
				}
			}
		}

		public function getUsername() {
			return $this->username;
		}

		public function getPassword() {
			return $this->password;
		}

	}
?>