CREATE OR REPLACE
PACKAGE BODY       API IS
    /*
        Access to any table inside database for foreign applications

        Table name must start with "table_prefix" and finish with "table_postfix"
        Transport protocol: JSON
    */


    function parse_str_to_json(in_str_json varchar2) return json
    /*
        Parsing string in_str_json, contains JSON to JSON-object
        Generating errors, if error in format
        in_str_json examples:
            {ID=1}
            {"table"="action",fields="ID,TYPE"}
            {names:[DEV_ID,"ID"],data:[[12,1],[13,2],[14,3]]}
    */
    is
        result json;
    begin
        begin
            result := json(in_str_json); -- convert json string to json object
        exception when others
        then
            raise_application_error(-20001,'Wrong JSON format:' || chr(13) || sqlerrm);
        end;
        return result;
    end;

    function prepare_select(in_param json) return varchar2
    /*
        Generates SELECT statement for given parameters in_param
    */
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
        select_str varchar2(2000);
        from_str varchar2(2000);
        finish_str varchar2(1000);
    begin
        -- gets names of parameters
        pkeys := in_param.get_keys;
        -- all parameters loop
        for i in 1 .. pkeys.count
        loop
            pkey := pkeys.get(i).get_string;
            ispvalnumeric := in_param.get(i).is_number;
            if ispvalnumeric
            then
                -- value is numeric
                pvalue := in_param.get(i).get_number;
            else
                -- value is string
                -- try to trim quotes, check numeric
                begin
                    pvalue := ltrim(rtrim(in_param.get(i).get_string,'"'),'"');
                    check_numeric := pvalue;
                    ispvalnumeric := true;
                exception when others
                then
                    -- value is not numeric
                    pvalue := replace(in_param.get(i).get_string,'"','\"');
                end;
            end if;
            -- analyze parameter names
            case
                when pkey = table_param
                then
                    -- parameter contains table name
                    source_str := table_prefix || pvalue || table_postfix;
                when pkey = fields_param
                then
                    -- parameter contains field list
                    field_str := pvalue;
                when pkey = offset_param
                then
                    -- parameter contains offset (must be numeric)
                    if ispvalnumeric
                    then
                        offset_count := pvalue;
                    else
                        raise_application_error(-20001, 'Value of parameter ' || pkey || ' must be numeric');
                    end if;
                when pkey = limit_param
                then
                    -- parameter contains rows count limit (must be numeric)
                    if ispvalnumeric
                    then
                        limit_count := pvalue;
                    else
                        raise_application_error(-20001, 'Value of parameter ' || pkey || ' must be numeric');
                    end if;
                when pkey = order_param
                then
                    -- parameter contains order field list
                    order_str := pvalue;
                else
                    -- parameter contains field name to filter
                    if ispvalnumeric
                    then
                        where_str := where_str || '"' || pkey || '"=' || pvalue || ' and ';
                    else
                        where_str := where_str || '"' || pkey || '"="' || pvalue || '" and ';
                    end if;
            end case;
        end loop;
        -- prepare where clause, if exists
        if where_str is not null
        then
            where_str := ' where ' || rtrim(where_str, 'and ');
        end if;
        -- prepare order-by clause, if exists
        if order_str is not null
        then
            order_str := ' order by ' || order_str;
        end if;
        -- prepare other constants
        select_str := 'select ';
        from_str := ' from ';
        finish_str := '';
        -- prepare offset and limit, if given
        if offset_count is not null or limit_count is not null
        then
            if field_str is null
            then
                -- return all fields from source
                field_str := 't.*';
                source_str := source_str || ' t';
                select_str := 'select * from (' || select_str;
            else
                -- fields given
                select_str := 'select ' || field_str || ' from (' || select_str;
            end if;
            from_str := ',row_number() over(ORDER BY 1) Z2DYQL7K' || from_str;  -- todo: checkout when ORDER parameter set
            if offset_count is not null
            then
                -- offset given
                finish_str := ') where Z2DYQL7K>' || to_char(offset_count);
                if limit_count is not null
                then
                    -- limit given
                    finish_str := finish_str || ' and Z2DYQL7K<=' || to_char(limit_count + offset_count);
                end if;
            else
                -- only limit given
                finish_str := ') where Z2DYQL7K<=' || to_char(limit_count);
            end if;
        else
            -- offset and limit is not set
            if field_str is null
            then
                -- field list is not set
                field_str := '*';
            end if;
        end if;
        return select_str || field_str || from_str || source_str || ' ' ||
            where_str || order_str || finish_str;
    end;

    function execute_select(select_str varchar2) return clob
    /*
        Executes any select query "select_str" and returns result in JSON string, CLOB

        There are two variants of resulting JSON: with field names
            on each row, or with separated field names (see body comments)
    */
    is
        result clob;
    begin
        begin
            -- prepare CLOB variable
            dbms_lob.createtemporary(result, true);
        exception when others
        then
            raise_application_error(-20001, 'Error prepare CLOB:' || chr(13) || sqlerrm);
        end;
        begin
            -- Without repeating field names
            --json.to_clob(json_dyn.executeobject(select_str), result);
            --select w_sys.api.select_rows('{table:device,fields:"ID,TYPE",limit:5}') from dual -- results:
            --{"names":["ID","TYPE"],"data":[[1,1],[2,1],[3,1],[4,1],[5,1]]}

            -- Field names repeating each row
            -- This list is not JSON, need to start from "{data:" and finish with "}" (see "return" in the end of function)
            json_list.to_clob(json_dyn.executeList(select_str),result);
            --select w_sys.api.select_rows('{table:device,fields:"ID,TYPE",limit:5}') from dual -- results:
            --[{"ID":1,"TYPE":1},{"ID":2,"TYPE":1},{"ID":3,"TYPE":1},{"ID":4,"TYPE":1},{"ID":5,"TYPE":1}]
        exception when others
        then
            raise_application_error(-20001, select_str || chr(13) || sqlerrm);
        end;
        --return result;
        -- JSON_LIST returns not JSON, fold it to "{data:" and "}"
        return '{data:' || result || '}';
    end;

    function select_rows(in_param_json varchar2) return clob
    /*
        Public function selects some rows from "table"
        See detailed description in package specification
    */
    is
        param json;
    begin
        -- parse json string to json object
        param := parse_str_to_json(in_param_json);
        return
            -- executes query, returns result in json string
            execute_select(
                -- build query, based on json parameters
                prepare_select(param)
            );
    end;

    function prepare_insert(in_object varchar2, in_param json) return varchar2
    /*
        Generates INSERT statement for given parameters in_param

        INSERT ALL construction does not work for views(when gets multiple rows),
            that's why, we have to use declare/begin/end statement
        Query:
            insert all
            into w_all.action#api ("DEV_ID","ID") VALUES (24,1)
            into w_all.action#api ("DEV_ID","ID") VALUES (24,2)
            select * from dual
        Results:
            ORA-01702: a view is not appropriate here
    */
    is
        data_jl json_list;
        vals_jl json_list;
        row_jv json_value;
        value_jv json_value;
        pjson json;
        fields_str varchar2(1000) := null;
        fields_predefined boolean := false;
        vals_str varchar2(4000);
        query_str varchar2(4000) := null;
    begin
        -- search data in parameters json
        if in_param.exist('data')
        then
            -- param 'data' found
            value_jv := in_param.get('data');
            if value_jv.is_array
            then
                -- param 'data' is an array
                data_jl := json_list(value_jv);
                -- search field names of insert data
                if in_param.exist('names')
                then
                    -- param 'names' found
                    value_jv := in_param.get('names');
                    if value_jv.is_array
                    then
                        -- param 'names' is an array
                        vals_jl := json_list(value_jv);
                        -- predefine field-names list 'fields_str'
                        for i in 1 .. vals_jl.count
                        loop
                            fields_str := fields_str || '"' || replace(vals_jl.get(i).get_string, '"', '') || '",';
                        end loop;
                        vals_jl := null;
                        fields_predefined := true;
                        fields_str := rtrim(fields_str, ',');
                        --raise_application_error(-20001, fields_str);
                    end if;
                end if;
            elsif value_jv.is_object
            then
                -- param 'data' is json object
                data_jl := json_list();
                data_jl.append(value_jv);
            else
                -- param 'data' is some field name
                data_jl := json_list();
                data_jl.append(json_value(in_param));
            end if;
        else
            -- all parameters is data
            data_jl := json_list();
            data_jl.append(json_value(in_param));
        end if;
        -- loops all rows in data
        for ind in 1 .. data_jl.count
        loop
            -- process each row in data
            row_jv := data_jl.get(ind);
            if row_jv.is_array
            then
                -- this row is an array in square brackets []
                vals_jl := json_list(row_jv);
            elsif row_jv.is_object
            then
                -- this row is an objects in brackets ()
                vals_jl := json(row_jv).get_values;
            else
                -- this row is some value without brackets, skip
                continue;
            end if;
            -- generate string of values of one row 'vals_str'
            vals_str := null;
            for i in 1 .. vals_jl.count
            loop
                value_jv := vals_jl.get(i);
                if value_jv.is_number
                then
                    vals_str := vals_str || value_jv.get_number || ','; --значение поля числовое
                else
                    vals_str := vals_str || '"' || replace(value_jv.to_char,'"','\"') || '",'; --значение поля строковое
                end if;
            end loop;
            if not fields_predefined
            then
                -- generate string of keys of one row 'fields_str'
                fields_str := '';
                vals_jl := json(row_jv).get_keys;
                for i in 1 .. vals_jl.count
                loop
                    fields_str := fields_str || vals_jl.get(i).get_string || ',';
                end loop;
                fields_str := rtrim(fields_str, ',');
            end if;
            if vals_str is not null
            then
                -- append query string with insert of new row
                query_str := query_str || 'insert into ' || table_prefix || in_object || table_postfix ||
                    ' (' || fields_str || ') VALUES (' || rtrim(vals_str, ',') || ');c:=c+sql%rowcount;' || chr(13);
            end if;
        end loop;
        if query_str is null
        then
            -- query is empty
            return '';
        else
            -- finish query and return the result
            return 'declare c number(20):=0;begin' || chr(13) || query_str || ':c:=c;end;';
        end if;
    end;

    function execute_query_c(query_str varchar2) return varchar2
    /*
        Executes a query, which have one out variable
        This variable returns count of modified rows

        Function returns JSON with fetched rows count:
        {rows:1}
        {rows:10}
    */
    is
        fetched number(20) := 0;
        pragma autonomous_transaction; -- allows DML operations inside a query
    begin
        if query_str is not null
        then
            begin
                execute immediate query_str using out fetched;
                commit;
            exception when others
            then
                raise_application_error(-20001, query_str || chr(13) || sqlerrm);
            end;
        end if;
        return '{rows:' || to_char(fetched) || '}';
    end;

    function insert_rows(in_source_name varchar2, in_param_json varchar2) return varchar2
    /*
        Public function inserts some rows to "in_source_name"
        See detailed description in package specification
    */
    is
        param json;
    begin
        -- parse json string to json object
        param := parse_str_to_json(in_param_json);
        return
            -- executes query, returning fetched rows in json string
            execute_query_c(
                -- build query, based on table name and json parameters
                prepare_insert(in_source_name, param)
            );
    end;


END API;
/
