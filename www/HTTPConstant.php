<?php
	//������������ ��� �������� ����������� ���������� � ������������������ � HTTP ���������
	class HTTPConstant {
		
		//��� ��������� � ������ ���� ������ �� ��������
		public static $okCode = '200';
		
		//��� ��������� �� ������ 400 BAD REQUEST
		public static $badRequestCode = '400';
		
		//��� ��������� �� ������ Unauthorized
		public static $unauthorizedCode = '401';
		
		//��� ��������� �� ������ Not Acceptable
		public static $notAcceptableCode = '406';
		
		//������� ��� ��������� HTTP ���������
		public static $HTTPPrefHeader = 'HTTP/1.1';
		
		//����� ��������� � ������ ���� ������ �� ��������
		public static $okMessage = 'Ok';
		
		//����� ��������� �� ������ 400 BAD REQUEST
		public static $badRequestMessage = 'BAD REQUEST';
		
		//����� ��������� �� ������ Unauthorized
		public static $unauthorizedMessage = 'Unauthorized';
		
		//����� ��������� �� ������ Not Acceptable
		public static $notAcceptableMessage = 'Not Acceptable';
		
		//��������� ��� �������� ��������� �������� � ����������� �� �����������
		public static $HTTPAuthHeader = 'WWW-Authenticate: Basic realm="MAN_login"';
		
		//������ ������������� � ������ ���� �� ������� �������� �� ����������� ������������
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