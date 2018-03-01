<?php
	/*
		Prepare dependency injector

		Useful links for understanding PHP-DI
			https://www.webcodegeeks.com/php/php-dependency-injection-tutorial/#section_6
			https://stackoverflow.com/questions/30616399/php-di-annotation-not-working
			http://php-di.org/doc/annotations.html
	*/

	require('vendor/autoload.php');			// prepare PHP-DI

	// Creating global object, capable creating any object, linked in Dependencies.php
	// Example of use inside classes:
	//		global $classes;
	//		$auth = $classes->get('Authorize');		// gets Authorize-linked class
	$builder = new DI\ContainerBuilder();
	$builder->useAnnotations(true);
	$builder->addDefinitions('Dependencies.php');
	$classes = $builder->build();

?>