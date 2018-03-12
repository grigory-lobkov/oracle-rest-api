<?php
	/*
		All output to user

		todo: make an interface

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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