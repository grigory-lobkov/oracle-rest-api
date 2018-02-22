<?php
	require('vendor/autoload.php');
	
	//Создание объекта, который будет доступен во всех классах приложения (global $container)
	//и благодаря этому можно будет внедрять необходимые объекты в классы
	$builder = new DI\ContainerBuilder();
	$builder->useAnnotations(true);
	$builder->addDefinitions('Dependencies.php');
	$container = $builder->build();
?>