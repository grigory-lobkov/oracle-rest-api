<?php
	/*
		All output to user

		todo: make an interface
	*/

	require_once('HTTPConstant.php');

	class HTTPView {

		/**
		 * Converts array to JSON and send as a text
		 */
		private function sendArrayAsJson($array, $header = '') {
			if (!headers_sent()) {
				header(HTTPConstant::$prefHeader . ' ' . $header);
			}
			echo json_encode($array);
		}
		
		/**
		 * Compute BadRequest error type to JSON and send
		 */
		public function sendBadRequest($errorText = '') {
			$array = HTTPConstant::$badRequest;
			$array['message'] .= '. ' . $errorText;
			$this->sendArrayAsJson(
				$array,
				HTTPConstant::$badRequestCode . ' ' . HTTPConstant::$badRequestMessage
			);
		}

		/**
		 * Compute Unauthorized error type to JSON and send
		 */
		public function sendUnauthorized($errorText = '') {
			$array = HTTPConstant::$unauthorized;
			$array['message'] .= '. ' . $errorText;
			$this->sendArrayAsJson(
				$array,
				HTTPConstant::$unauthorizedCode . ' ' . HTTPConstant::$unauthorizedMessage
			);
		}

		/**
		 * Send text with status 200 OK
		 */
		public function sendText($text) {
			if (!headers_sent()) {
				header(HTTPConstant::$prefHeader . ' ' . HTTPConstant::$okCode . ' ' . HTTPConstant::$okMessage);
			}
			echo $text;
		}

	}

?>