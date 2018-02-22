<?php
//Подключение класса интерфейса
	include_once 'IDataBase.php';
	
//Класс работает на уровне модели, необходим для взаимодействия с базой Oracle
	class OracleDB implements IDataBase{
		
		public $connection;
		private $username;
		private $password;
		private $host;
		private $port;
		private $sid;
//Кодировка базы данных
		private $dbencoding;
		private $connectiontype;
		
//Вызов процедур в Oracle для назначения роли по умолчанию для пользователя
		const DEFAULT_ROLE_INIT_SQL_STR=<<<EOL
			BEGIN
				w_sys.DbAccRoles.GrantMeRole('W_ALL_ROLES');
			END;
EOL;
		const TASK_ROLE_INIT_SQL_STR=<<<EOL
	    DECLARE
                arm_role_name w_sys.menu.task_role%TYPE:=NULL;
            BEGIN 
                begin
                    select 
                        task_role
                    into 
                        arm_role_name
                    from 
                        W_SYS.REL_MENU_USER#SYS1
                    where 
                        m_id=:taskid;
                    w_sys.DbAccRoles.GrantMeRole(arm_role_name); 
                exception when NO_DATA_FOUND then
                    RAISE_APPLICATION_ERROR(-20001,'Main user role not found ');
                end;
            END;
EOL;
		
//Для любого действия классу OracleDB необходимы адрес сервера, CID-базы и тип подключения к БД
//Поэтому эти параметры добавлены в конструктор класса. Параметр $connectiontype может быть не задан
		function __construct($host,$port,$sid,$dbencoding,$connectiontype) {
/*			if (!isset($username)) throw new Exception('User name is not set');
			if (strlen($username)==0) throw new Exception('User name is null');
			if (!isset($password)) throw new Exception('Password is not set');
			if (strlen($password)==0) throw new Exception('Password is null');*/
			if (!isset($host)) throw new Exception('Host is not set');
			if (strlen($host)==0) throw new Exception('Host is null');
			if (!isset($port)) throw new Exception('Port is not set');
			if (strlen($port)==0) throw new Exception('Port is null');
			if (!isset($sid)) throw new Exception('Sid is not set');
			if (strlen($sid)==0) throw new Exception('Sid is null');
			if (!isset($dbencoding)) throw new Exception('Data base encoding is not set');
			if (strlen($dbencoding)==0) throw new Exception('Data base encoding is null');
			$this->host=$host;
			$this->port=$port;
			$this->sid=$sid;
			$this->dbencoding=$dbencoding;
			if (isset($connectiontype)) $this->connectiontype=$connectiontype;
		}
		
//Метод предназначен для подключения к БД и записи ссылки на подключение в поле $connection класса
		public function connect(){
//Конвертирую все warning в сообщения об ошибках т.к. в случае неудачной попытки подключения к БД
//появляется warning а не error
			set_error_handler(function($errno, $errstr, $errfile, $errline){
    			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			});
			$conn = oci_pconnect($this->username,$this->password, '//'.$this->host
				.':'.$this->port.'/'.$this->sid.((isset($this->connectiontype))?':'.$this->connectiontype:''),$this->dbencoding);
//Востанавливаю стандарную обработку сообщений от операторов
			restore_error_handler();
			$this->connection=$conn;
		}
		
//Метод предназначен для отключения от БД
		public function disconnect($connection) {
			oci_close($connection);
		}
		
//Метод предназначен для получения значения поля со сслыкой на подключения к БД
		public function getConnection(){
			return $this->connection;
		}
		
		public function setUserName($username){
			$this->username=$username;
		}
		
		public function setPassword($password){
			$this->password=$password;
		}
		
//Метод для предоставлению пользователю прав для выборки объектов из БД
		public function setRole($taskid){
//Получить идентификатор подключивщегося пользователя и установить для пользователя роли 
//доступные всем пользователям для выборки персональных ролей для работы с задачами
//Скрипт работы с ролью разделен на два фрагмента т.к. выборка W_SYS.AVAIL_ROLES не работает
//Пока не будут выданы роли W_ALL_ROLE
			$stid=oci_parse($this->connection,self::DEFAULT_ROLE_INIT_SQL_STR);
			oci_execute($stid);
//Выбрать для пользователя основную роль для работы в М3
			$stid=oci_parse($this->connection,self::TASK_ROLE_INIT_SQL_STR);
			oci_bind_by_name($stid,':taskid',$taskid);
			oci_execute($stid);
			oci_free_statement($stid);
		}
		
	}
?>
