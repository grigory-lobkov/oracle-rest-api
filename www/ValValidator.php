<?php
	/*
		Check user data
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