<?php
//Класс предназначен для объединения методов по проверке правильности ввода значений в поля
	
	class ValValidator {
		
//Метод необходим для проверки полей типа "Имя пользователя" на корректность ввода
		public function isValidUserName($username,$strmask='/^[-а-яА-Яa-zA-Z0-9_]+$/u',$resp='Unresolved characters in user name'){
			if (!preg_match($strmask,$username)) throw new Exception($resp);
			return true;
		}

//Метод необходим для проверки полей типа "Пароль" на корректность ввода
		public function isValidPassword($passwd,$strmask='/^[-а-яА-Яa-zA-Z0-9_!@#$%^&*()=+|]+$/u',$resp='Unresolved characters in password'){
			if (!preg_match($strmask,$passwd)) throw new Exception($resp);
			return true;
		}
		
	}
?>