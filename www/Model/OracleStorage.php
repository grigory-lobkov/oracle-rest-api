<?php
	/*
		Access to Oracle storage procecdures
		Calls API package functions, ensure, user have access to it

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	require_once('IDataStorage.php');		// interface class

	class OracleStorage implements IDataStorage{

		private $connection;
		private $view;

		/**
		 * Query for select rows from database (calls API package, select_rows function)
		 */
		const CALL_SELECT_DATA =
			'select w_sys.api.select_rows(:filter) from dual';

		/**
		 * Query for insert rows into database (calls API package, insert_rows function)
		 */
		const CALL_INSERT_DATA =
			'select w_sys.api.insert_rows(:source, :data) from dual';

		/**
		 * Query for delete rows from database (calls API package, delete_rows function)
		 */
		const CALL_DELETE_DATA =
			'select w_sys.api.delete_rows(:filter) from dual';

		/**
		 * Query for update rows in database (calls API package, update_rows function)
		 */
		const CALL_UPDATE_DATA =
			'select w_sys.api.update_rows(:filter, :data) from dual';

		/**
		 * Query for get data from some field in database (calls API package, get_field function)
		 */
		const CALL_GET_FIELD =
			'select w_sys.api.get_field(:filter) from dual';

		/**
		 * Query for put data in some field in database (calls API package, put_field function)
		 */
		const CALL_SET_FIELD =
			'select w_sys.api.set_field(:filter, :data) from dual';

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
				var_dump($e);
				$this->view->sendBadRequest('parse. ' . $e['message']);
			}
			return $result;
		}

		/**
		 * Oracle oci_execute function, generating exception on error
		 */
		private function execute($statement) {
			$result = oci_execute($statement);
			if (!$result) {
				$e = oci_error($statement);
				$this->view->sendBadRequest('execute. ' . $e['message']);
			}
			return $result;
		}

		/**
		 * Requests DB for data in JSON format
		 */
		public function select($filterStrJSON) {
			$stmt = $this->parse(self::CALL_SELECT_DATA);
			if ($stmt) {
				oci_bind_by_name($stmt, ':filter', $filterStrJSON, 4000, SQLT_CHR);
				$result = $this->execute($stmt);
				if ($result) {
					while (list($json) = oci_fetch_array($stmt)) {
						$this->view->sendText($json->load());
					}
				}
				oci_free_statement($stmt);
			}
		}

		/**
		 * Inserts data in JSON format to DB
		 */
		public function insert($sourceNameStr, $dataStrJSON) {
			$stmt = $this->parse(self::CALL_INSERT_DATA);
			if ($stmt) {
				oci_bind_by_name($stmt, ':source', $sourceNameStr, 50, SQLT_CHR);
				oci_bind_by_name($stmt, ':data', $dataStrJSON, 4000, SQLT_CHR);
				$result = $this->execute($stmt);
				if ($result) {
					while (list($json) = oci_fetch_array($stmt)) {
						$this->view->sendText($json);
					}
				}
				oci_free_statement($stmt);
			}
		}

		/**
		 * Removes data from DB
		 */
		public function delete($filterStrJSON) {
			$stmt = $this->parse(self::CALL_DELETE_DATA);
			if ($stmt) {
				oci_bind_by_name($stmt, ':filter', $filterStrJSON, 4000, SQLT_CHR);
				$result = $this->execute($stmt);
				if ($result) {
					while (list($json) = oci_fetch_array($stmt)) {
						$this->view->sendText($json);
					}
				}
				oci_free_statement($stmt);
			}
		}

		/**
		 * Updates data in DB
		 */
		public function update($filterStrJSON, $dataStrJSON) {
			$stmt = $this->parse(self::CALL_UPDATE_DATA);
			if ($stmt) {
				//$filterStrJSON = '{table:action,DEV_ID:24,TYPE:3}';
				oci_bind_by_name($stmt, ':filter', $filterStrJSON, 4000, SQLT_CHR);
				oci_bind_by_name($stmt, ':data', $dataStrJSON, 4000, SQLT_CHR);
				$result = $this->execute($stmt);
				if ($result) {
					while (list($json) = oci_fetch_array($stmt)) {
						$this->view->sendText($json);
					}
				}
				oci_free_statement($stmt);
			}
		}

		/**
		 * Gets data from one field of source of DB and send it without header
		 */
		public function getField($filterStrJSON) {
			$stmt = $this->parse(self::CALL_GET_FIELD);
			if ($stmt) {
				oci_bind_by_name($stmt, ':filter', $filterStrJSON, 4000, SQLT_CHR);
				$result = $this->execute($stmt);
				if ($result) {
					while (list($clob) = oci_fetch_array($stmt)) {
						if (!is_null($clob)) {
							echo $clob->load();
						}
					}
				}
				oci_free_statement($stmt);
			}
		}

		/**
		 * Puts data in one field of source of DB
		 */
		public function setField($filterStrJSON, $data) {
			$stmt = $this->parse(self::CALL_SET_FIELD);
			if ($stmt) {
				$clob = oci_new_descriptor($this->connection, OCI_DTYPE_LOB);
				oci_bind_by_name($stmt, ':filter', $filterStrJSON, 4000, SQLT_CHR);
				oci_bind_by_name($stmt, ':data', $clob, -1, SQLT_BLOB);
				$clob->writetemporary($data);
				$result = $this->execute($stmt);
				if ($result) {
					while (list($json) = oci_fetch_array($stmt)) {
						$this->view->sendText($json);
					}
				}
				$clob->free();
				oci_free_statement($stmt);
			}
		}

	}

?>