<?php
	require('vendor/autoload.php');
	
	//�������� �������, ������� ����� �������� �� ���� ������� ���������� (global $container)
	//� ��������� ����� ����� ����� �������� ����������� ������� � ������
	$builder = new DI\ContainerBuilder();
	$builder->useAnnotations(true);
	$builder->addDefinitions('Dependencies.php');
	$container = $builder->build();
?>