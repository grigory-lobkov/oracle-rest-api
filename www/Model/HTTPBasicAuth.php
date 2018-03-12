<?php
	/*
		Authentification, using Http Basic Authentification

		Description of methods watch inside interface class

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
			$this->username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
			$this->password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
		}

		public function check() {
			global $container;
			if (!isset($this->username) || $this->username == '') {
				// username is not set
				header('WWW-Authenticate: Basic realm="php-db-rest-api"');
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