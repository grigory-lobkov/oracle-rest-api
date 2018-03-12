<?php
	/*
		Storage of HTTP-specific constants

		Copyright (c) 2018 Grigory Lobkov
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
		LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
		IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
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
			'more_info'=>'Please, check request parameters'
		);

		// User not found or have a bad name/password
		public static $unauthorized = array(
    		'status'=>'401',
			'message'=>'Unauthorized user',
			'more_info'=>'Please, check your username/password'
		);

	}
?>