<?php
	/*
		Database storage interface
		Provide access to database data

		Saves capability to change authorization method

		used in: OracleStorage.php
	*/

	interface IDataStorage {

		/**
		 * Sets the connection to DB resource
		 */
		public function setConnection($connection);

		/**
		 * Requests DB for data in JSON format
		 */
		public function selectDataToJSON($requestParam);

		/**
		 * Inserts data in JSON format to DB
		 */
		public function insertJSONDataToDB($sourceName, $insertParam);

	}

?>