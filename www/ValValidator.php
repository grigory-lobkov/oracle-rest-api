<?php
	/*
		Check user data

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
	*/

	class ValValidator {

		/**
		 * Check, if username have only valid characters
		 */
		public function isValidUserName(
			$username,
			$strmask = '/^[-а-яА-Яa-zA-Z0-9_]+$/u',
			$resp = 'Unresolved characters in user name'
		) {
			if (!preg_match($strmask, $username)) throw new Exception($resp);
			return true;
		}

		/**
		 * Check, if password have only valid characters
		 */
		public function isValidPassword(
			$passwd,
			$strmask = '/^[-а-яА-Яa-zA-Z0-9_!@#$%^&*()=+|]+$/u',
			$resp = 'Unresolved characters in password'
		) {
			if (!preg_match($strmask, $passwd)) throw new Exception($resp);
			return true;
		}

	}

?>