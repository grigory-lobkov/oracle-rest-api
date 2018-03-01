<?php
	/*
		Authorization interface
		Authorize users to access API server

		Saves capability to change authorization method

		used in: HTTPBasicAuth.php
	*/

	interface IAuthorization {

		/**
		 * Defining of default username/password
		 */
		function __construct();

		/**
		 * Start check of username/password (validation)
		 */
		public function check();

		/**
		 * Get user name
		 */
		public function getUsername();

		/**
		 * Get user password
		 */
		public function getPassword();

	}

?>