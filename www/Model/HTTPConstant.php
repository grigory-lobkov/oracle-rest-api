<?php
	/*
		Storage of HTTP-specific constants
	*/

	class HTTPConstant {

		//No-errors code
		public static $okCode = '200';

		// BAD REQUEST code
		public static $badRequestCode = '400';

		// UNAUTHORIZED code
		public static $unauthorizedCode = '401';

		// prefix for HTTP header
		public static $prefHeader = 'HTTP/1.1';

		// No-errors message text
		public static $okMessage = 'Ok';

		// BAD REQUEST message text
		public static $badRequestMessage = 'BAD REQUEST';

		// UNAUTHORIZED message text
		public static $unauthorizedMessage = 'Unauthorized';

		// Header containing authorize information
		public static $authHeader = 'WWW-Authenticate: Basic realm="MAN_login"';

		// Bad request data
		public static $badRequest = array(
    		'status'=>'400',
			'message'=>'Bad Request',
			'more_info'=>'http:\\api.m3ati.ru_net_documentation_url'
		);

		// User not found or have a bad name/password
		public static $unauthorized = array(
    		'status'=>'401',
			'message'=>'Unauthorized user',
			'more_info'=>'http:\\api.m3ati.ru_net_documentation_url'
		);

	}
?>