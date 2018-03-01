CREATE OR REPLACE
PACKAGE BODY       API IS
/* Пакет добавил Лобков Г.Г. 06.02.2018
Предназначен для хранения объектов и процедур необходимых для работы api для обращения к Oracle
из сторонних приложений */

type var_array is table of varchar(20);
type var_array_indexed is table of number(1) index by varchar(20);

-- константы для обращения к объектам базы, запросы из таблиц:
--device_select_tpl constant varchar2(50) := 'select {fields} from w_all.device#api';
--device_select_fields constant var_array := var_array('ID','TYPE','STRCODE','PLACEMENT','PARENT_ID','DEP_ID');
-- имена таблиц для вставки
--action_insert_table constant varchar2(12) := 'w_all.action';

    function parse_str_to_json(in_str_json varchar2)
/* Добавил Лобков Г.Г. 09.02.2018
Преобразует из строки в json с генерацией ошибки
in_str_json - строка, содержащая объект json в {} */
    return json
    is
        result json;
    begin
        --преобразование строки json в объект json
        begin
            result := json(in_str_json);
        exception when others then
            raise_application_error(-20001,'Wrong JSON format:' || chr(13) || sqlerrm);
        end;
        return result;
    end;

    function prepare_select(/*in_select_tpl varchar2, in_select_fields var_array,*/in_param json)
/*Добавил Гусев Д.Г. 30.09.2016
07.02.2018 Лобков Г.Г. переделал на ввод и вывод в json, перенес в MAN.W_SYS.API из M3ATI.ATI.API
Фунция формирует текст запроса на основании переданных параметров запроса и шаблона
in_tpl_query - шаблон для построения запроса
in_param - параметры запроса */
    return varchar2
    is
        pkeys json_list;
        pkey varchar2(100);
        pvalue varchar2(2000);
        ispvalnumeric boolean;
        check_numeric number(20);
        source_str varchar2(100) := null;
        field_str varchar2(2000) := null;
        where_str varchar2(2000) := null;
        order_str varchar2(100) := null;
        offset_count number(20) := null;
        limit_count number(20) := null;
--        select_str varchar2(4000) := in_select_tpl;
        offset_select_bg varchar2(2200) := null;
        offset_fld varchar2(46) := null;
        offset_select_end varchar2(100) := null;
        select_fields var_array_indexed;
        select_str varchar2(200):='select {fields} from  w_all.';
        alias_str varchar2(8):='mainSrc';
        view_ext_str varchar2(8):='#api';
    begin
        --переводим массив полей из значений в индексы, чтобы работал .exists
/*        for i in in_select_fields.first .. in_select_fields.last loop
            select_fields(in_select_fields(i)) := 1;
        end loop;*/
        --проходим по всем переданным параметрам:
        pkeys := in_param.get_keys; --получаем имена полей json
        for i in 1 .. pkeys.count --делаем по ним цикл
        loop
            pkey := pkeys.get(i).get_string; --имя поля
            ispvalnumeric := in_param.get(i).is_number;
            if ispvalnumeric then
                pvalue := in_param.get(i).get_number; --значение поля числовое
            else
                begin
                    pvalue := ltrim(rtrim(in_param.get(i).get_string,'"'),'"');
                    check_numeric := pvalue; --если значение поля таки числовое, но в кавычках
                    ispvalnumeric := true;
                exception when others then
                    pvalue := replace(in_param.get(i).get_string,'"','\"'); --значение поля строковое
                end;
            end if;
            case
                --Сохранить в переменную основной источник для выборки
                when pkey = table_param then
                    source_str:= pvalue;
                -- определить поля для отображения
                when pkey = fields_param then
                    field_str := field_str || pvalue || ',';
                -- вернуть элементы с заданного смещения
                when pkey = offset_param then
                    if ispvalnumeric then
                        offset_count := pvalue;
                    else
                        raise_application_error(-20001, 'Value of parametr ' || pkey || ' must be numeric');
                    end if;
                -- ограничение на количество возвращаемых элементов
                when pkey = limit_param then
                    if ispvalnumeric then
                        limit_count := pvalue;
                    else
                        raise_application_error(-20001, 'Value of parametr ' || pkey || ' must be numeric');
                    end if;
                -- сортировка результатов выборки
                when pkey = order_param then
                    order_str := order_str || pvalue || ',';
                else
--                    if select_fields.exists(pkey) then -- pkey совпадает с одним из имен полей (имя поля в верхнем регистре)
                        if ispvalnumeric then
                            where_str := where_str || '"' || pkey || '"=' || pvalue || ' and ';
                        else
                            where_str := where_str || '"' || pkey || '"="' || pvalue || '" and ';
                        end if;
/*                    else
                        select_str := replace(select_str, '{in_' || pkey || '}', pvalue);*/
--                    end if;
            end case;
        end loop;
        if field_str is not null then
            field_str := rtrim(field_str, ',');
        else
            field_str := alias_str||'.*';
        end if;
        if where_str is not null then
            where_str := ' where ' || rtrim(where_str, 'and ');
        end if;
        -- так как функции offset нет в oracle 11g, то вместо нее используется расчетное поле номер строки
        if offset_count is not null or limit_count is not null then
--            offset_select_bg := 'select ' || field_str || ' from (';
            offset_select_bg := 'select * from (';
            offset_fld := ',row_number() over(ORDER BY 1) NUM9dhgiehnvak';
            if offset_count is not null then
                offset_select_end := ') where NUM9dhgiehnvak>' || to_char(offset_count);
                if limit_count is not null then
                    offset_select_end := offset_select_end || ' and NUM9dhgiehnvak<=' || to_char(limit_count + offset_count);
                end if;
            else
                offset_select_end := ') where NUM9dhgiehnvak<=' || to_char(limit_count);
            end if;
        end if;
        -- формируем запрос
        if  length(order_str) > 0 then
            order_str := ' order by ' || rtrim(order_str, ',');
            --if offset_select_end is not null then --повторная сортировка уже отсортированного?
            --    offset_select_end := offset_select_end || order_str;
            --end if;
        end if;
--        return offset_select_bg || replace(select_str, '{fields}', field_str || offset_fld)
--            || where_str || order_str || offset_select_end;
        return offset_select_bg || replace(select_str, '{fields}', field_str || offset_fld)
            ||source_str||view_ext_str||' '||alias_str
            || where_str || order_str || offset_select_end;
    end;

    function execute_select(select_str varchar2)
/* Добавил Лобков Г.Г. 09.02.2018
Выполняет запрос и возвращает результат строкой, содержащей json в {}
select_str - полностью готовая к выполнению строка запроса select oracle */
    return clob
    is
        result clob;
        js json_list;
    begin
        begin
            dbms_lob.createtemporary(result, true); -- готовим clob
        exception when others then
            raise_application_error(-20001, 'Error preparing CLOB:' || chr(13) || sqlerrm);
        end;
        begin
            --json.to_clob(json_dyn.executeobject(select_str), result); -- без повторяющихся имен колонок
            --select w_sys.api.select_device('{ID:27}') from dual -- вернет:
            --{"names":["ID","TYPE","STRCODE","PLACEMENT","PARENT_ID","DEP_ID"],"data":[[27,4,"\u0413\u043E\u043B\u043E\u0432\u043A\u0430 \u0432\u0435\u0441\u043E\u0432\u0430\u044F \u21162","\u041F\u0440\u0435\u0441\u0441\u043E\u0432\u043E\u0435 \u043E\u0442\u0434\u0435\u043B\u0435\u043D\u0438\u0435 \u0446\u0435\u0445\u0430 \u0410\u0424\u0414, \u0420\u043E\u0442\u043E\u0440\u043D\u044B\u0439 \u043F\u0440\u0435\u0441\u0441 \u21162",22,2]]}
            json_list.to_clob(json_dyn.executeList(select_str),result); -- список, не являющийся json - надо добавлять "{data:" и "}" в начало и конец соответственно
            --select w_sys.api.select_device('{ID:27}') from dual -- вернет:
            --[{"ID":27,"TYPE":4,"STRCODE":"\u0413\u043E\u043B\u043E\u0432\u043A\u0430 \u0432\u0435\u0441\u043E\u0432\u0430\u044F \u21162","PLACEMENT":"\u041F\u0440\u0435\u0441\u0441\u043E\u0432\u043E\u0435 \u043E\u0442\u0434\u0435\u043B\u0435\u043D\u0438\u0435 \u0446\u0435\u0445\u0430 \u0410\u0424\u0414, \u0420\u043E\u0442\u043E\u0440\u043D\u044B\u0439 \u043F\u0440\u0435\u0441\u0441 \u21162","PARENT_ID":22,"DEP_ID":2}]
        exception when others then
            raise_application_error(-20001, select_str || chr(13) || sqlerrm);
        end;
        return '{data:' || result || '}';
    end;

    function execute_query(query_str varchar2)
/* Добавил Лобков Г.Г. 09.02.2018
Выполняет запрос и возвращает результат строкой, содержащей json в {}
query_str - полностью готовая к выполнению строка запроса oracle */
    return varchar2
    is
        fetched number(20);
        PRAGMA AUTONOMOUS_TRANSACTION;
    begin
        if query_str is null then return '{rows:0}'; end if;
        begin
            execute immediate query_str;
            fetched := SQL%ROWCOUNT;
            commit;
            return '{rows:' || to_char(fetched) || '}';
        exception when others then
            raise_application_error(-20001, query_str || chr(13) || sqlerrm);
        end;
    end;

    function select_rows(in_param_json varchar2)
/* Добавил Лобков Г.Г. 07.02.2018
Функция возвращяет JSON-строку с результатами выборки по указанным параметрам
in_param_json - параметры запроса в json формате */
    return clob
    is
        param json;
    begin
        param := parse_str_to_json(in_param_json);  -- получаем из строки объект json
        return
            execute_select(         -- возвращаем результат выполнения запроса, обернутый в json
                prepare_select(     -- готовим запрос в соответствии с параметрами
--                    device_select_fields,   -- поля, по которым возможен фильтр
                    param                   -- параметры выполнения
                )
            );
    end;

    function prepare_simple_insert(in_object varchar2, in_param json)
/* Добавил Лобков Г.Г. 07.02.2018
Функция необходима для вставки событий контроллера в простую таблицу, за один insert
in_param_json - параметры вставки, варианты:
{DEV_ID:12}
{data:[{DEV_ID:12,ID:1},{ID:13},{DEV_ID:14}]}
{names:[DEV_ID,"ID"],data:[[12,1],[13,2],[14,3]]}
15.02.2018 Гусев Д.Г. переделал вставку. До изменений функция вставляла данные в таблицы
, после изменений в представления. Вставка в представления дает возможность подменить тригер на вставку
в представление и за один вызов функции вставить данные во все необходимые таблицы.
Недостаток такого подхода в том, что Oracle 11 запрещает Insert all во view
(Пример:
    Запрос:
    insert all into w_all.action#api ("DEV_ID","ID") VALUES (24,1)
    into w_all.action#api ("DEV_ID","ID") VALUES (24,2)
    select * from dual
    Результат:
    ORA-01702: a view is not appropriate here
)
*/
    return varchar2
    is
        param json;
        param_list json_list;
        tmp_json_value json_value;
        pkeys json_list;
        pkey varchar2(100);
        pvalue varchar2(4000);
        fields_str varchar2(1000);
        fields_set boolean;
        vals_str varchar2(4000);
        query_str varchar2(4000) := '';
        ind numeric(10) := 0;
        insert_schema_str varchar2(8):='w_all.';
        insert_ext_str varchar2(8):='#api';
    begin
/*        if not in_param.exist('data') then
            tmp_json_value := in_param);
        else
            tmp_json_value := in_param.get('data');
        end if;*/
--        raise_application_error(-20001, 'in_param= ' || in_param.to_char);
        if in_param.exist('data') then -- обнаружено поле с данными для вставки
            tmp_json_value := in_param.get('data');
            if tmp_json_value.is_array then
                param_list := json_list(tmp_json_value);
                if param_list.count = 0 then return ''; end if; -- если массив данных пуст
                if in_param.exist('names') then -- заранее заданы имена полей
                    tmp_json_value := in_param.get('names');
                    if tmp_json_value.is_array then
                        pkeys := json_list(tmp_json_value);
                        for i in 1 .. pkeys.count loop
                            pkey := pkeys.get(i).get_string;
                            fields_str := fields_str || '"' || replace(pkey, '"', '') || '",';
                        end loop;
                        fields_set := true;
                        fields_str := rtrim(fields_str, ',');
                    else
                        param := json(param_list.get(1));
                    end if;
                else
                    param := json(param_list.get(1));
                end if;
                ind := 1;
            elsif tmp_json_value.is_object then
                param := json(tmp_json_value);
            else
                param := in_param;
            end if;
        else
            param := in_param;
        end if;
        if fields_set then -- поля установлены заранее
            while ind <= param_list.count
            loop
                pkeys := json_list(param_list.get(ind));
                vals_str := '';
                for i in 1 .. pkeys.count
                loop
                    tmp_json_value := pkeys.get(i);
                    if tmp_json_value.is_number then
                        vals_str := vals_str || tmp_json_value.get_number || ','; --значение поля числовое
                    else
                        --vals_str := vals_str || '"' || replace(tmp_json_value.to_char,'"','\"') || '",'; --значение поля строковое
                        vals_str := vals_str || '"' || replace(tmp_json_value.to_char,'"','''') || '",'; --значение поля строковое
                    end if;
                end loop;
                query_str := query_str || 'into ' || insert_schema_str || in_object || insert_ext_str || ' (' || fields_str
                    || ') VALUES (' || rtrim(vals_str, ',') || ')' || chr(13);
                ind := ind + 1;
            end loop;
        else -- поля берем из имен значений объекта
            loop
                fields_str := '';
                vals_str := '';
                pkeys := param.get_keys; --получаем имена полей json
                for i in 1 .. pkeys.count --делаем по ним цикл
                loop
                    pkey := pkeys.get(i).get_string; --имя поля
                    tmp_json_value := param.get(i);
                    if tmp_json_value.is_number then
                        vals_str := vals_str || tmp_json_value.get_number || ','; --значение поля числовое
                    else
                        --vals_str := vals_str || replace(tmp_json_value.to_char,'"','\"') || ','; --значение поля строковое
                        vals_str := vals_str || replace(tmp_json_value.to_char,'"','''') || ','; --значение поля строковое
                    end if;
                    fields_str := fields_str || pkey || ',';
                end loop;
                query_str := query_str || 'into ' || insert_schema_str || in_object || insert_ext_str || ' (' || rtrim(fields_str, ',')
                    || ') VALUES (' || rtrim(vals_str, ',') || ')' || chr(13);
                if ind = 0 then exit; end if;
                ind := ind + 1;
                if ind > param_list.count then
                    exit;
                else
                    param := json(param_list.get(ind));
                end if;
            end loop;
        end if;
        if ind = 0 or ind = 2 then
            return 'insert ' || query_str;
        elsif ind > 2 then
            return 'insert all ' || query_str || 'select * from dual';
        else
            return '';
        end if;
    end;

    function insert_rows(in_source_name varchar2, in_param_json varchar2)
/* Добавил Лобков Г.Г. 07.02.2018
Функция необходима для вставки данных переданных в формате JSON в соответсвующие поля указанного источника
in_source_name - название таблицы/представления для вставки данных
in_param_json - строка в формате json */
    return varchar2
    is
        param json;
    begin
        param := parse_str_to_json(in_param_json);  -- получаем из строки объект json
        return
            execute_query(              -- выполняем запрос
                prepare_simple_insert(  -- готовим вставку
                    in_source_name,    -- имя таблицы
                    param                   -- параметры запроса
                )
            );
    end;


END API;
/
