<?php
//Класс предназначен для реализации конкретного типа авторизации Http Basic Authentification 
//выделен в отдельный класс для того чтобы при необходимости тип авторизации можно было поменять
	
//Подключение класса интерфейса для всех реализаций подключения
	require_once('IAuthorization.php');
	require_once('HTTPConstant.php');

	class HTTPBasicAuth implements IAuthorization{
		
		/**
	     * Annotation combined with phpdoc:
	     *
	     * @Inject
	     * @var View
	     */
	    private $view;
		
		public function checkAuthorize($username,$password,$valvalidator,IDataBase $db){
//			global $container;
//			$view = $container->get('MainView');
//Часть кода для принудительного сброса Authorization в случае необходимости
/*			if(isset($_GET['unAuth'])) {
				header('WWW-Authenticate: Basic realm="M3_login"');
//				header('HTTP/1.1 401 Unauthorized');
//				die ("Not authorized");
				$this->view->sendResponse('401',HTTPConstant::$unauthorized);
			}*/
			
			
			if (!isset($username)||empty($username)) {
			    header(HTTPConstant::$HTTPAuthHeader);
				$this->view->sendResponse(HTTPConstant::$unauthorizedCode, HTTPConstant::$unauthorized);
			} else {
//			    echo "<p>Hello {$username}.</p>";
//			    echo "<p>Вы ввели пароль {$password}.</p>";
			    try {
			    	$valvalidator->isValidUserName($username);
			    	$valvalidator->isValidPassword($password);
			    } catch (Exception $e) {
					$addUnauthorized=HTTPConstant::$unauthorized;
					$addUnauthorized['message']=$addUnauthorized['message'].'. '.$e->getMessage();
					header(HTTPConstant::$HTTPAuthHeader);
					$this->view->sendResponse(HTTPConstant::$unauthorizedCode, $addUnauthorized);
				}
				$db->setUserName($username);
				$db->setPassword($password);
				try {
					$db->connect();
				} catch (Exception $e) {
					$addUnauthorized=HTTPConstant::$unauthorized;
					$addUnauthorized['message']=$addUnauthorized['message'].'. '.$e->getMessage();
					header(HTTPConstant::$HTTPAuthHeader);
					$this->view->sendResponse(HTTPConstant::$unauthorizedCode, $addUnauthorized);
				}
			    
			}
		}
		
		
	}
?>