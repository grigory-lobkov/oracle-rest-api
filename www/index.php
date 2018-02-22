<?php
//w_action1 / r1gn5 - пресса 1, 2 нитки в АФД

//Подключаю скрипт для особых настроек на время отладки. Удалить на рабочем сервере
//Заменил include_once на require_once т.к. при использовании include_once скрипт 
//не останавливается в случае если файла с указанным именем не найдено, а в случае 
//require_once останоавливается с ошибкой
	require_once('DebugMode.php');
//Подключить модуль с построеним зависимотсей для всех классов
	require_once('Controller.php');
	require_once('HTTPConstant.php');
	require_once('Config.php');
	
	//Класс приложения с которого начинается загрузка
	class Main{
		
		//Конструктор Main
		function __construct() {
			//Переменная содержащая построитель зависимостей действующая все время существования глобального 
			//контекста, объявлена в классе Controller
			global $container;
			//Создание объекта View происходит в контэйнере. Обращение к контэйнеру сделано через 
			//get, а не через Inject для того чтобы первое обращение всегда происходило после создания 
			//построителя зависимостей ($container)
			$view = $container->get('MainView');
			//Создание экземпляра объекта для подмены стандартных методов
			$methodModificator = $container->get('MethodModif');
			$methodModificator->enable();
			$db = $container->get('DataBase');
			$username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
			$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
			$auth = $container->get('HTTPBasicAuth');
			$valValidator = $container->get('ValValidator');
			$auth->checkAuthorize($username,$password,$valValidator,$db);
			//В строке запроса к api необходимо всегда передавать параметр taskId для того чтобы api-сервер 
			//мог выдать соотвествующие права по роли превязанной к задаче с указанным tskId. 
			//В процессе работы требования изменились и потребовалось добавить возможность для авторизации 
			//пользователя по имени и паролю с теми правами, которые есть у пользователя (без ролей)
			if(!isset($_GET['taskId'])||!is_numeric($_GET['taskId'])) {
			
			} else {
				$taskid = $_GET['taskId'];
				try {
					$db->setRole($taskid);
				} catch (Exception $e) {
					$addExecErr = HTTPConstant::$badRequest;
					$addExecErr['message'] = $addExecErr['message'] . '. ' . $e->getMessage();
					$view->sendResponse(HTTPConstant::$badRequestCode, $addExecErr);
				}
			}
//Добавить обработку для обращений к различным представлениям БД, доступным через API (возможно обращения к таблицам лучше вынести в отдельный класс Repository)
			$requestParser = $container->get('RequestParser');
			try {
				$requestParser->prepareStatment($_SERVER["REQUEST_METHOD"],$_SERVER["QUERY_STRING"]
					,Config::$urlParamDelim,$db->getConnection());
			} catch (Exception $e) {
				$addExecErr=HTTPConstant::$badRequest;
				$addExecErr['message']=$addExecErr['message'].'. '.$e->getMessage();
				$view->sendResponse(HTTPConstant::$badRequestCode,$addExecErr);
			}
			//Разрыв соединения
			$db->disconnect($db->getConnection());
		}
		
	}
	
	//Создание экземпляра класса и вызов метода конструктора для начала работы
	$main = new Main();
	
?>