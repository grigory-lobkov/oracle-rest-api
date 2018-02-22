<?php
//Класс необходим для хранения метод для изменения поведения методов стадартных классов или подключаемых бибилиотек php
//Не стал переопределять oci_pconnect т.к. функция используется один раз и переопределение получилось бы больше чем 
//Преобразование warning в error прямо в коде
	class MethodModificator {
		
//На момент разработки класса было найдено мало объектов поведение методов в которых требуется заменить
//поэтому включение замены всех модифицируемых методов собрано в один метод
		public function enable(){
			if(function_exists('_oci_execute')) runkit_function_remove('_oci_execute');
//			runkit_function_rename('oci_execute','_oci_execute');
//runkit_function_add('oci_execute','$q',<<<EOF
//_oci_execute($q);
//EOF
//);
			runkit_function_copy('oci_execute','_oci_execute');
			runkit_function_redefine('oci_execute', 
				function ($q) {
					set_error_handler(function($errno, $errstr, $errfile, $errline){
    					throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
					});
					_oci_execute($q);
					restore_error_handler();
				}
			);
		}
		
	}
?>