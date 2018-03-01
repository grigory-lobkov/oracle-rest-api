<?php
	/*
		Main class, process everething
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

			try {

				// Create user data parser
				$parser = $classes->get('Parser');
				$parser->setConnection($connection);
				// Execute user request
				$parser->parse(
					$_SERVER["REQUEST_METHOD"],				// GET / POST
					@$_GET["table"],						// device
					json_encode($_GET, JSON_NUMERIC_CHECK), // {table:device,ID:5}
					file_get_contents('php://input')		// {DEV_ID:24,WEIGHT:0,TYPE:1}
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