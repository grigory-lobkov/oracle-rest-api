<?php
//Интерфейс для всех классов предназначенных для подключения к БД
//Интерфейс добавлен на случай если произойдет смена БД
	interface IDataBase {

//Метод необходим для установки соединения с БД
		public function connect();
//Метод предназначен для получения значения поля со сслыкой на подключения к БД
		public function getConnection();
//Метод необходим для установки значения переменной имени польвателя
		public function setUserName($username);
//Метод необходим для установки значения переменной пароля польвателя
		public function setPassword($password);
		
	}
?>