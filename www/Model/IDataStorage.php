<?php
//Интефрейс для всех классов предназначенных для выборок из БД
	interface IDataStorage {
		
		//Передать ссылку на подключение к БД
		public function setConnection($connection);
		
		//Метод необходим для выборки данных из БД в формате JSON
		function selectDataToJSON($requestParam);
		
		//Метод необходим для передачи данных в формате JSON для вставки в БД
		public function insertJSONDataToBD($sourceName,$requestParam);
	
	}
	
?>