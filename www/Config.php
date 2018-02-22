<?php
	
	class Config{
	
		//IP-адрес сервера БД
		public static $dbServerAddress = '192.168.0.31';
		//Порт сервера БД
		public static $dbServerPort = '1521';
		//Имя сервера БД
		public static $dbName = 'MAN';
		//Кодировка сервера БД
		public static $dbEncoding = 'UTF8';
		//Тип подключения к БД (использовать пул или нет)
		public static $dbConnectionType = 'POOLED';
		//Обяъвляю символ, который будет являться разделителем между параметрами запроса к серверу
		public static $urlParamDelim = '&';
	
	}
?>