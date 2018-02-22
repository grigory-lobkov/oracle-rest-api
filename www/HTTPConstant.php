<?php
	//Предназначен для хранения статических переменных с зарезервированными в HTTP терминами
	class HTTPConstant {
		
		//Код сообщения в случае если ошибок не возникло
		public static $okCode = '200';
		
		//Код сообщения об ошибке 400 BAD REQUEST
		public static $badRequestCode = '400';
		
		//Код сообщения об ошибке Unauthorized
		public static $unauthorizedCode = '401';
		
		//Код сообщения об ошибке Not Acceptable
		public static $notAcceptableCode = '406';
		
		//Префикс для заголовка HTTP сообщения
		public static $HTTPPrefHeader = 'HTTP/1.1';
		
		//Текст сообщения в случае если ошибок не возникло
		public static $okMessage = 'Ok';
		
		//Текст сообщения об ошибке 400 BAD REQUEST
		public static $badRequestMessage = 'BAD REQUEST';
		
		//Текст сообщения об ошибке Unauthorized
		public static $unauthorizedMessage = 'Unauthorized';
		
		//Текст сообщения об ошибке Not Acceptable
		public static $notAcceptableMessage = 'Not Acceptable';
		
		//Константа для хранения заголовка страницы с информацией об авторизации
		public static $HTTPAuthHeader = 'WWW-Authenticate: Basic realm="MAN_login"';
		
		//Ошибка ототбражается в случае если не найдены сведенья об авторизации пользователя
		public static $badRequest = array(
    		'status'=>'400',
			'message'=>'Bad Request',
			'more_info'=>'http:\\api.m3ati.ru_net_documentation_url'
		);
		
		public static $unauthorized = array(
    		'status'=>'401',
			'message'=>'Unauthorized user',
			'more_info'=>'http:\\api.m3ati.ru_net_documentation_url'
		);
		
		public static $notAccetable = array(
    		'status'=>'406',
			'message'=>'Not Acceptable',
			'more_info'=>'http:\\api.m3ati.ru_net_documentation_url'
		);
		
	}
?>