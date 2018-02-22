<?php
	//Клсс предназначен для хранения статических переменных с сообщениями (Например: об ошибках)
	class Messages {
		
	//Текст сообщения об ошибке в случае если передан неизвестный тип HTTP Заголовка
		public static $unknowHTTPHeaderErr = 'Unknow header';
		
		public static $TableNameNotFoundErr = '. Table name not found in URL';
		
		public static $PostDataNotSetErr = 'POST parameter "data" is not set';
	
	}
?>