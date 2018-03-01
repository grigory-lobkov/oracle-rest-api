<?php
	/*
		Oracle database connection

		Description of methods watch inside interface class
	*/

	include_once 'IDataBase.php';		// interface class

	class OracleDB implements IDataBase{

		private $connection;
		private $host;
		private $port;
		private $sid;
		private $encoding;
		private $connectiontype;

		public function __construct($dbServerAddress, $dbServerPort, $dbName, $dbEncoding, $dbConnectionType = '') {
			// saving income parameters
			$this->host = $dbServerAddress;
			$this->port = $dbServerPort;
			$this->sid = $dbName;
			$this->encoding = $dbEncoding;
			$this->connectiontype = $dbConnectionType;
		}

		public function connect($username, $password) {
			global $classes;
			$connection = oci_pconnect(
				$username,
				$password,
				'//' . $this->host . ':' . $this->port . '/' . $this->sid .
					(($this->connectiontype == '') ? '' : ':' . $this->connectiontype),
				$this->encoding
			);
			if (!$connection) {
				$e = oci_error();
				$view = $classes->get('View');
				$view->sendUnauthorized($e['message']);
			} else {
				$this->connection = $connection;
			}
			return $connection;
		}

		public function getConnection(){
			return $this->connection;
		}

		public function disconnect() {
			oci_close($this->connection);
		}

	}

?>