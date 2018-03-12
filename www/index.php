<?php
	/*
		Main class, process everething

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	require_once('DebugMode.php');			// for develop (show errors), comment this line for production
	require_once('Controller.php');			// init dependencies (global $classes)

	class Main{

		/**
		 *	Get user gata, login, connecting to DB, parsing results, sending to user
		 */
		function __construct() {

			// Load DI container with dependencies
			global $classes;

			// Creating Authorize object (described in Dependencies.php), obtain default user/password
			$auth = $classes->get('Authorize');

			// Check of user/password
			$auth->check();

			// Creating DataBase object (described in Dependencies.php)
			$db = $classes->get('DataBase');

			// Connecting to database with user/password
			$connection = $db->connect($auth->getUsername(), $auth->getPassword());
			if(!$connection) die();

			try {

				// Create user data parser
				$parser = $classes->get('Parser');
				$parser->setConnection($connection);
				// Execute user request
				$parser->parse(
					$_SERVER["REQUEST_METHOD"],				// GET / POST / PUT / DELETE / PATH
					@$_GET["table"],						// device
					json_encode($_GET, JSON_NUMERIC_CHECK), // {table:device,ID:5}
					isset($_SERVER["HTTP_CONTENT_LENGTH"]) ?// -- to be able to set empty value
						file_get_contents('php://input')	// {DEV_ID:24,WEIGHT:0,TYPE:1} // todo: if contents is not empty and not present, send "false"
						: false
				);
			} catch (Exception $e) {
				// Creating user-output object
				$view = $classes->get('View');
				// Output an error
				$view->sendBadRequest($e->getMessage());
			}

			// Close connection to database
			$db->disconnect();

		}

	}

	// Run
	$main = new Main();

?>