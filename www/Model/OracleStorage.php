<?php
	/*
		Access to Oracle storage procecdures
		Calls API package functions, ensure, user have access to it
	*/

	require_once('IDataStorage.php');		// interface class

	class OracleStorage implements IDataStorage{

		private $connection;
		private $view;

		/**
		 * Query for select rows from database (calls API package, select_rows function)
		 */
		const CALL_SELECT_DATA_TO_JSON =
			'select w_sys.api.select_rows(:strParam) from dual';

		/**
		 * Query for insert rows into database (calls API package, insert_rows function)
		 */
		const CALL_INSERT_JSON_DATA_TO_DB =
			'select w_sys.api.insert_rows(:sourceName, :strParam) from dual';

		public function __construct() {
			global $classes;
			// inject dependencies
			$this->view = $classes->get('View');
		}

		/**
		 * Sets the connection to DB resource
		 */
		public function setConnection($connection) {
			$this->connection = $connection;
		}

		/**
		 * Oracle oci_parse function, generating exception on error
		 */
		private function parse($query) {
			$result = oci_parse($this->connection, $query);
			if (!$result) {
				$e = oci_error($this->connection);
				$this->view->sendBadRequest($e['message']);
			}
			return $result;
		}

		/**
		 * Oracle oci_execute function, generating exception on error
		 */
		private function execute($statement) {
			$result = oci_execute($statement);
			if (!$result) {
				$e = oci_error($this->connection);
				$this->view->sendBadRequest($e['message']);
			}
			return $result;
		}

		/**
		 * Requests DB for data in JSON format
		 */
		public function selectDataToJSON($requestParam) {
			$stid = $this->parse(self::CALL_SELECT_DATA_TO_JSON);
			if ($stid) {
				oci_bind_by_name($stid, ':strParam', $requestParam, 4000, SQLT_CHR);
				$result = $this->execute($stid);
				if ($result) {
					while (list($json) = oci_fetch_array($stid)) {
						$this->view->sendText($json->load());
					}
				}
				oci_free_statement($stid);
			}
		}

		/**
		 * Inserts data in JSON format to DB
		 */
		public function insertJSONDataToDB($sourceName, $insertParam) {
			$stid = $this->parse(self::CALL_INSERT_JSON_DATA_TO_DB);
			if ($stid) {
				oci_bind_by_name($stid, ':sourceName', $sourceName, 50, SQLT_CHR);
				oci_bind_by_name($stid, ':strParam', $insertParam, 4000, SQLT_CHR);
				$result = $this->execute($stid);
				if ($result) {
					while (list($json) = oci_fetch_array($stid)) {
						$this->view->sendText($json);
					}
				}
				oci_free_statement($stid);
			}
		}

	}

?>