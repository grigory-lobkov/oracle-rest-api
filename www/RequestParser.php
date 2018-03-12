<?php
	/*
		Parsing user requests and redirect it to database

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
					$this->dataStorage->select($requestData);
					break;
				// New data (insert)
				case 'POST':
					if ($postData == '') {
						throw new Exception(Messages::$PostDataNotSetErr);
					}
					$this->dataStorage->insert($requestSource, $postData);
					break;
				// Change/Get one field (update/select)
				case 'PATH':
					if ($postData === false) {
						$this->dataStorage->getField($requestData);
					} else {
						$this->dataStorage->setField($requestData, $postData);
					}
					break;
				// Change data (update)
				case 'PUT':
					if ($postData == '') {
						throw new Exception(Messages::$PostDataNotSetErr);
					}
					$this->dataStorage->update($requestData, $postData);
					break;
				// Delete data (delete)
				case 'DELETE':
					$this->dataStorage->delete($requestData);
					break;
				// Unknown method
				default:
					throw new Exception(Messages::$RequestMethodUnknownErr);
			}
		}

	}

?>