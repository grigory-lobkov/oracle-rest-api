CREATE OR REPLACE
PACKAGE       API AUTHID CURRENT_USER AS
/* Пакет добавил Лобков Г.Г. 06.02.2018, переработав пакет M3ATI.ATI.API (Гусев Д.Г. 30.08.2016)
Предназначен для хранения объектов и процедур необходимых для работы api для обращения к Oracle
из сторонних приложений */

    table_param varchar2(5) := 'table';
    fields_param varchar2(6) := 'fields';
    order_param varchar2(5) := 'order';
    offset_param varchar2(6) := 'offset';
    limit_param varchar2(5) := 'limit';


    function select_rows(in_param_json varchar2)
/* Добавил Лобков Г.Г. 07.02.2018
Функция возвращяет JSON-строку с результатами выборки по указанным параметрам
in_param_json - параметры запроса в json формате */
    return clob;


    function insert_rows(in_source_name varchar2, in_param_json varchar2)
/* Добавил Лобков Г.Г. 07.02.2018
Функция необходима для вставки данных переданных в формате JSON в соответсвующие поля указанного источника
in_source_name - название таблицы/представления для вставки данных
in_param_json - строка в формате json */
    return varchar2;



END API;
/
