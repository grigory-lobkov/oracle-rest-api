<?php
	/*
		Database interface
		Makes connection to database

		Saves capability to change database

		used in: OracleDB.php
	*/

	interface IDataBase {

		/**
		 * Sets all config parameters (class no reason to live without this parameters)
		 */
		public function __construct($dbServerAddress, $dbServerPort, $dbName, $dbEncoding, $dbConnectionType = '');

		/**
		 * Connecting to database with specified username and password, returns connection resource
		 */
		public function connect($username, $password);

		/**
		 * Returns the connection resource
		 */
		public function getConnection();

		/**
		 * Close connection to database
		 */
		public function disconnect();

	}

?>