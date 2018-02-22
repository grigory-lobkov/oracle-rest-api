<?php
//Класс предназначен для группировки запросов к БД Oracle
	require_once('IDataStorage.php');
	
	class OracleStorage implements IDataStorage{
		
		private $connection;
		
		//Текст запроса для выборки данных из указанного источника по указанным параметрам
		const CALL_SELECT_DATA_TO_JSON = 'select w_sys.api.select_rows(:strParam)from dual';
		
		//Текст запроса для передачи данных в формате JSON для вставки в БД
		const CALL_INSERT_JSON_DATA_TO_BD = 'select w_sys.api.insert_rows(:sourceName,:strParam)from dual';
		
		//Передать ссылку на подключение к БД
		public function setConnection($connection){
			$this->connection=$connection;
		}
		
		//Метод необходим для выборки данных из БД в формате JSON
		public function selectDataToJSON($requestParam) {
			$stid = oci_parse($this->connection, self::CALL_SELECT_DATA_TO_JSON);
//			$stid = oci_parse($db, 'select w_sys.api.' . $pref . $source_name . '(:strParam)from dual');
			oci_bind_by_name($stid, ':strParam', $requestParam, 4000, SQLT_CHR);
			$result = oci_execute($stid);
			if ($result !== false) {
				while (list($json) = oci_fetch_array($stid)) {
					echo $json->load();
				}
			}
			die();
			oci_free_statement($stid);
		}
		
		//Метод необходим для передачи данных в формате JSON для вставки в БД
		public function insertJSONDataToBD($sourceName,$requestParam) {
			$stid = oci_parse($this->connection, self::CALL_INSERT_JSON_DATA_TO_BD);
			oci_bind_by_name($stid, ':sourceName', $sourceName, 4000, SQLT_CHR);
			oci_bind_by_name($stid, ':strParam', $requestParam, 4000, SQLT_CHR);
			$result = oci_execute($stid);
			if ($result !== false) {
				while (list($json) = oci_fetch_array($stid)) {
					echo $json;
				}
			}
			die();
			oci_free_statement($stid);
		}
	
	}
	
?>