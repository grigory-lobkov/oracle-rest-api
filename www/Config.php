<?php
	/*
		Configuration of database server
	*/

	class Config{

		// User-exchange protocol
		// Supported: HTTP
		public static $protocol = 'HTTP';

		// Authorization method
		// Supported: Basic
		public static $auth = 'Basic';

		// Database server (DB) type
		// Supported: Oracle
		public static $dbType = 'Oracle';
		
		// DB address
		public static $dbServerAddress = '192.168.0.31';

		// DB port
		public static $dbServerPort = '1521';

		// DB name, SID for Oracle
		public static $dbName = 'MAN';

		// DB encoding
		public static $dbEncoding = 'UTF8';

		// DB connection type
		// for Oracle "POOLED" is better, need to be enabled on the server: dbms_connection_pool.start_pool();
		public static $dbConnectionType = 'POOLED';

	}

?>