<?php
	require_once('vendor/autoload.php');
	require_once('Config.php');
	require_once('View.php');
	require_once('MethodModificator.php');
	require_once('Model/OracleDB.php');
	require_once('Model/HTTPBasicAuth.php');
	require_once('ValValidator.php');
	require_once('RequestParser.php');
	require_once('Model/OracleStorage.php');

//Файл-граф описания зависимостей объектов

	return [
	    'MainView' => DI\Object('View'),
	    'MethodModif' => DI\Object('MethodModificator'),
	    'DataBase' => DI\Object('OracleDB')
	    	->constructor(Config::$dbServerAddress, Config::$dbServerPort
	    		, Config::$dbName, Config::$dbEncoding, Config::$dbConnectionType
		),
	    'HTTPBasicAuth' => DI\Object('HTTPBasicAuth'),
	    'ValValidator' => DI\Object('ValValidator'),
	    'RequestParser' => DI\Object(),
	   	'OracleStorage' => DI\Object(),

	];
?>