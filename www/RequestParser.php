<?php
	require_once('Messages.php');
	
//Класс предназначен для объединения методов по обработке текста http-запроса
	class RequestParser{
		
		/**
	     * Annotation combined with phpdoc:
	     *
	     * @Inject
	     * @var OracleStorage
	     */
	    private $dataStorage;
		
//Разбирает переданный в параметре текст на параметры необходимые для запроса к api-функциям в БД
//В $requestParam решено было не передавать массив GET т.к. один из клиентов api delphi 7 через ODAC 6.8
//В котором не удается передать массив в параметр select
		public function prepareStatment($requestMethod,$requestParam,$param_delim, $connection){
//Список зарезервированных слов
/*			$sysrow=array('limit','offset','table','fields');
			$fltArray=array();
			foreach ($paramArray as $k=>$v)
				if(array_search($v,$sysrow)===false)//Если элемент массива не в списке служебных полей значит по нему можно попробовать отфильтровать выборку
					$fltArray[]=$k.'='.$v;
			echo(' table='.$paramArray['table']);*/
//Поиск имени таблицы в параметрах запроса. Имя источника данных должно присутсвовать в URL обязательно
			$source_tag = 'table=';
			if (isset($_GET['table'])) {
				$sourceName = $_GET['table'];
			} else {
				throw new Exception(Messages::$TableNameNotFoundErr);
			}
			/*$source_pos_bg=strpos($requestParam,$source_tag);
			if ($source_pos_bg===false) {
				throw new Exception('. Table name not found in URL');
			} else {
				$source_pos_end=strpos($requestParam,'&',$source_pos_bg)-strlen($source_tag);
				if (!isset($source_pos_end)) $source_pos_end=strlen($requestParam);
				$sourceName=substr($requestParam,$source_pos_bg+strlen($source_tag),$source_pos_end);
			}*/
			$this->dataStorage->setConnection($connection);
//Генерация обращения к БД в зависимости от типа заголовка
			switch ($requestMethod) {
//Выборка данных (select)
    			case 'GET':
//					$pref = 'select_';
					$requestParam = json_encode($_GET, JSON_NUMERIC_CHECK);
					
					$this->dataStorage->selectDataToJSON($requestParam);
					die($source_tag);
					break;
//Создать запись (insert)
				case 'POST':
//					$pref = 'insert_';
//В POST запросе данные могут быть либо в поле data либо в виде JSON в массиве POST
					if (!isset($_POST['data'])){
						if(!isset($_POST)){
							throw new Exception(Messages::$PostDataNotSetErr);
						}
						else{
							//Получить содержимое POST запроса в виде строки
							$requestParam = file_get_contents('php://input');
						}
					}
					else {
						$requestParam = $_POST['data'];
					}
					$this->dataStorage->insertJSONDataToBD($sourceName, $requestParam);
					break;
//Изменить все поля записи (update)
				case 'PUT':
					
					break;
//Изменить значение одного поля записи (update)
				case 'PATH':
					
					break;
//Удаление записи (delete)
				case 'DELETE':
					
					break;
			}
			
//Проверка выполнения запроса
			
			
		}
		
	}
?>