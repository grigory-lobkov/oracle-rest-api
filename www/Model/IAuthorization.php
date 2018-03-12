<?php
	/*
		Authorization interface
		Authorize users to access API server

		Saves capability to change authorization method

		used in: HTTPBasicAuth.php

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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