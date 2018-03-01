<?php
	/*
		Parsing user requests and redirect it to database
	*/

	require_once('Messages.php');		// load messages

	class RequestParser{

	    private $dataStorage;

		function __construct() {
			global $classes;
			// inject dependencies
			$this->dataStorage = $classes->get('DataStorage');
		}

		function setConnection($connection) {
			$this->dataStorage->setConnection($connection);
		}

		/**
		 * Transmit request parameters to DataStorage
		 */
		public function parse($requestMethod, $requestSource, $requestData, $postData) {
			global $classes;
			if ($requestSource == '') {
				throw new Exception(Messages::$TableNameNotFoundErr);
			}
			switch ($requestMethod) {
				// Get data (select)
    			case 'GET':
					$this->dataStorage->selectDataToJSON($requestData);
					break;
				// New data (insert)
				case 'POST':
					if ($postData == '') {
						throw new Exception(Messages::$PostDataNotSetErr);
					}
					$this->dataStorage->insertJSONDataToDB($requestSource, $postData);
					break;
				// Change one item (update)
				case 'PATH':
				// Change data (update)
				case 'PUT':
					die('Not working yet, sorry');	// todo
					break;
				// Delete data (delete)
				case 'DELETE':
					die('Not working yet, sorry');	// todo
					break;
				// Unknown method
				default:
					throw new Exception(Messages::$RequestMethodUnknownErr);
			}
		}

	}

?>