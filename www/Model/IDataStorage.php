<?php
	/*
		Database storage interface
		Provide access to database data

		Saves capability to change authorization method

		used in: OracleStorage.php

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	interface IDataStorage {

		/**
		 * Sets the connection to DB resource
		 */
		public function setConnection($connection);

		/**
		 * Requests DB for data in JSON format
		 */
		public function select($filterStrJSON);

		/**
		 * Inserts data in JSON format to DB
		 */
		public function insert($sourceNameStr, $dataStrJSON);

		/**
		 * Removes data from DB
		 */
		public function delete($filterStrJSON);

		/**
		 * Updates data in DB
		 */
		public function update($filterStrJSON, $dataStrJSON);

		/**
		 * Gets data from one field of source of DB and send it without header
		 */
		public function getField($filterStrJSON);

		/**
		 * Puts data in one field of source of DB
		 */
		public function setField($filterStrJSON, $data);

	}

?>