<?php
//Интерфейс для всех классов предназначенных для авторизации клиента на сервере php.
//Интерфейс добавлен т.к. механиз (реализция) процесса авторизации может измениться но 
//принципы должны остаться для сохранения работоспособности других модулей
//Используется в модуле HTTPBasicAuth.php
	interface IAuthorization {
		//Метод необходим для проверки авторизации либо запуска
		public function checkAuthorize($username,$password,$valvalidator,IDataBase $db);
	}
?>