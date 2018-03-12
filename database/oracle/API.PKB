CREATE OR REPLACE
PACKAGE BODY       API IS
    /*
        Access to any table inside database for foreign applications

        Table name must start with "table_prefix" and finish with "table_postfix"
        Transport protocol: JSON

        Copyright (c) 2018 Grigory Lobkov
        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
        TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
        THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
        CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
        DEALINGS IN THE SOFTWARE.
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
            raise_application_error(-20001,'Wrong JSON format: ' || sqlerrm);
        end;
        return result;
    end;

    function prepare_select(in_filter json) return varchar2
    /*
        Generates SELECT statement for given parameters in_filter
    */
    is
        keys_jl json_list;
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
        keys_jl := in_filter.get_keys;
        -- all parameters loop
        for i in 1 .. keys_jl.count
        loop
            pkey := keys_jl.get(i).get_string;
            ispvalnumeric := in_filter.get(i).is_number;
            if ispvalnumeric
            then
                -- value is numeric
                pvalue := in_filter.get(i).get_number;
            else
                -- value is string
                -- try to trim quotes, check numeric
                begin
                    pvalue := ltrim(rtrim(in_filter.get(i).get_string,'"'),'"');
                    check_numeric := pvalue;
                    ispvalnumeric := true;
                exception when others
                then
                    -- value is not numeric
                    pvalue := replace(in_filter.get(i).get_string,'"','\"');
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
        -- error, if source is not set
        if source_str is null
        then
            raise_application_error(-20001, 'Parameter `' || table_param || '` is mandatory');
        end if;
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
        -- compose query and return
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
            raise_application_error(-20001, 'Error prepare CLOB: ' || sqlerrm);
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
            raise_application_error(-20001, select_str || ' ' || sqlerrm);
        end;
        --return result;
        -- JSON_LIST returns not JSON, fold it to "{data:" and "}"
        return '{data:' || result || '}';
    end;

    function select_rows(in_filter_json varchar2) return clob
    /*
        Public function selects some rows from "table"
        See detailed description in package specification
    */
    is
        filterj json;
    begin
        -- parse json string to json object
        filterj := parse_str_to_json(in_filter_json);
        return
            -- executes query, returns result in json string
            execute_select(
                -- build query, based on json filter parameters
                prepare_select(filterj)
            );
    end;

    function prepare_insert(in_object varchar2, in_data json) return varchar2
    /*
        Generates INSERT statement for given parameters in_data

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
        if in_data.exist('data')
        then
            -- param 'data' found
            value_jv := in_data.get('data');
            if value_jv.is_array
            then
                -- param 'data' is an array
                data_jl := json_list(value_jv);
                -- search field names of insert data
                if in_data.exist('names')
                then
                    -- param 'names' found
                    value_jv := in_data.get('names');
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
                data_jl.append(json_value(in_data));
            end if;
        else
            -- all parameters is data
            data_jl := json_list();
            data_jl.append(json_value(in_data));
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
                    vals_str := vals_str || value_jv.get_number || ',';
                else
                    vals_str := vals_str || '"' || replace(value_jv.to_char,'"','\"') || '",';
                end if;
            end loop;
            if not fields_predefined
            then
                -- generate string of keys of one row 'fields_str'
                fields_str := '';
                vals_jl := json(row_jv).get_keys;
                for i in 1 .. vals_jl.count
                loop
                    fields_str := fields_str || '"' || vals_jl.get(i).get_string || '",';
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
            -- compose query and return the result
            return 'declare c number(20):=0;begin' || chr(13) || query_str || ':c:=c;end;';
        end if;
    end;

    function execute_insert(insert_str varchar2) return varchar2
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
        if insert_str is not null
        then
            begin
                execute immediate insert_str
                    using out fetched;
                commit;
            exception when others
            then
                raise_application_error(-20001, insert_str || ' ' || sqlerrm);
            end;
        end if;
        return '{rows:' || to_char(fetched) || '}';
    end;

    function insert_rows(in_source_str varchar2, in_data_json varchar2) return varchar2
    /*
        Public function inserts some rows to "in_source_str"
        See detailed description in package specification
    */
    is
        dataj json;
    begin
        -- parse json string to json object
        dataj := parse_str_to_json(in_data_json);
        return
            -- executes query, returning fetched rows in json string
            execute_insert(
                -- build query, based on table name and json parameters
                prepare_insert(in_source_str, dataj)
            );
    end;


    function prepare_delete(in_filter json) return varchar2
    /*
        Generates DELETE statement for given parameters in_filter
    */
    is
        filterj json;
        query_str varchar2(4000);
    begin
        -- check, if fields set is provided
        if in_filter.exist(fields_param)
        then
            raise_application_error(-20001, 'Parameter `' || fields_param || '` is not acceptable');
        end if;
        filterj := in_filter;
        -- add out fields definition
        filterj.put(fields_param,'rowid');
        -- generate select on deleted rows
        query_str := prepare_select(filterj);
        -- compose and return deleting query
        return 'delete from ' || table_prefix || filterj.get(table_param).get_string || table_postfix ||
            ' where rowid in (' || query_str || ')';
    end;

    function execute_query(query_str varchar2) return varchar2
    /*
        Executes a query

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
                execute immediate query_str;
                fetched := sql%rowcount;
                commit;
            exception when others
            then
                raise_application_error(-20001, query_str || ' ' || sqlerrm);
            end;
        end if;
        return '{rows:' || to_char(fetched) || '}';
    end;

    function delete_rows(in_filter_json varchar2) return varchar2
    /*
        Public function removes some rows from in_filter_json."table"
        See detailed description in package specification
    */
    is
        filterj json;
    begin
        -- parse json string to json object
        filterj := parse_str_to_json(in_filter_json);
        return
            -- executes query, returning fetched rows count in json string
            execute_query(
                -- build query, based on filter json parameters
                prepare_delete(filterj)
            );
    end;


    function prepare_update(in_filter json, in_data json) return varchar2
    /*
        Generates UPDATE statement for in_data with filter, provided in_filter
    */
    is
        filterj json;
        query_str varchar2(4000);
        update_str varchar2(4000) := null;
        keys_jl json_list;
        value_jv json_value;
    begin
        -- check, if fields set is provided
        if in_filter.exist(fields_param)
        then
            raise_application_error(-20001, 'Parameter `' || fields_param || '` is not acceptable');
        end if;
        filterj := in_filter;
        -- add out fields definition
        filterj.put(fields_param,'rowid');
        -- generate select on deleted rows
        query_str := prepare_select(filterj);
        -- generate updating fields string
        keys_jl := in_data.get_keys;
        for i in 1 .. keys_jl.count
        loop
            -- field name
            update_str := update_str || '"' || replace(keys_jl.get(i).get_string,'"','\"') || '"=';
            -- field value
            value_jv := in_data.get(i);
            if value_jv.is_number
            then
                update_str := update_str || value_jv.get_number || ',';
            else
                update_str := update_str || '"' || replace(value_jv.to_char,'"','\"') || '",';
            end if;
        end loop;
        if update_str is null
        then
            -- no update data provided
            return '';
        else
            -- compose and return updating query
            return 'update ' || table_prefix || filterj.get(table_param).get_string || table_postfix ||
                ' set ' || rtrim(update_str, ',') || ' where rowid in (' || query_str || ')';
        end if;
    end;

    function update_rows(in_filter_json varchar2, in_data_json varchar2) return varchar2
    /*
        Public function updates some rows from in_filter_json."table"
        See detailed description in package specification
    */
    is
        filterj json;
        dataj json;
    begin
        -- parse json string to json object
        filterj := parse_str_to_json(in_filter_json);
        dataj := parse_str_to_json(in_data_json);
        return
            -- executes query, returning fetched rows count in json string
            execute_query(
                -- build query, based on filter and data json parameters
                prepare_update(filterj, dataj)
            );
    end;


    function prepare_get_field(in_filter json) return varchar2
    /*
        Generates FieldSelect statement, filtered by in_filter
    */
    is
        filterj json;
        getField_str varchar2(4000);
    begin
        -- check, if fields set is provided
        if not in_filter.exist(fields_param)
        then
            raise_application_error(-20001, 'Parameter `' || fields_param || '` is mandatory');
        end if;
        filterj := in_filter;
        -- get selected field name
        getField_str := filterj.get(fields_param).get_string;
        filterj.remove(fields_param);
        -- modify field name parameter
        filterj.put(fields_param,'('||getField_str||')');
        -- generate FieldSelect and return it
        return prepare_select(filterj);
    end;

    function execute_get_field(select_str varchar2) return blob
    /*
        Executes one field select query "select_str" and returns raw result, BLOB
    */
    is
        blobTbl dbms_sql.blob_table;
        clobTbl dbms_sql.clob_table;
        clobSelect varchar2(4000);

        function clob2blob(in_clob clob) return blob
        -- Converts CLOB in_clob to BLOB and return it
        is
            result           blob;
            loc_dest_offsset integer := 1;
            loc_src_offsset  integer := 1;
            loc_lang_context integer := dbms_lob.default_lang_ctx;
            loc_warning      integer := 0;
        begin
            if in_clob is null
            then
                -- nothing to do
                return null;
            end if;
            -- prepare CLOB
            dbms_lob.createTemporary(result, true);
            -- convert
            dbms_lob.converttoblob(
                dest_lob     => result,
                src_clob     => in_clob,
                amount       => dbms_lob.lobmaxsize,
                dest_offset  => loc_dest_offsset,
                src_offset   => loc_src_offsset,
                blob_csid    => dbms_lob.default_csid,
                lang_context => loc_lang_context,
                warning      => loc_warning);
            return result;
        exception when others
        then
            raise;
        end;
    begin
        -- check if query is empty
        if select_str = ''
        then
            return null;
        end if;
        begin
            -- try to fill the results array of BLOB
            execute immediate select_str
                bulk collect into blobTbl;
            if blobTbl.count > 0
            then
                -- return first value of results set
                return blobTbl(1);
            end if;
        exception when others
        then
            -- ORA-00932: inconsistent datatypes, expected BLOB, get CLOB/VARCHAR2/NUMBER
            if sqlcode = -932
            then
                begin
                    -- try to get the result array of CLOB
                    clobSelect := 'select to_clob' || substr(select_str,8);
                    execute immediate clobSelect
                        bulk collect into clobTbl;
                    if clobTbl.count > 0
                    then
                        -- convert to blob and return first value of results set
                        return clob2blob(clobTbl(1));
                    end if;
                exception when others
                then
                    raise_application_error(-20001, clobSelect || ' ' || sqlerrm);
                end;
            else
                raise_application_error(-20001, select_str || ' ' || sqlerrm);
            end if;
        end;
        return null;
    end;

    function get_field(in_filter_json varchar2) return blob
    /*
        Public function to get one field value from in_filter_json."table"
        See detailed description in package specification
    */
    is
        filterj json;
    begin
        -- parse json string to json object
        filterj := parse_str_to_json(in_filter_json);
        return
            -- executes query, returning one field value
            execute_get_field(
                -- build query, based on json parameter
                prepare_get_field(filterj)
            );
    end;


    function prepare_set_field(in_filter json) return varchar2
    /*
        Generates FieldSet statement, filtered by in_filter
    */
    is
        select_str varchar2(4000);
        filterj json;
        updateField_str varchar2(4000);
    begin
        -- check, if fields set is provided
        if not in_filter.exist(fields_param)
        then
            raise_application_error(-20001, 'Parameter `' || fields_param || '` is mandatory');
        end if;
        filterj := in_filter;
        -- get updated field name
        updateField_str := filterj.get(fields_param).get_string;
        filterj.remove(fields_param);
        -- get updated rows select
        filterj.put(fields_param,'rowid');
        select_str := prepare_select(filterj);
        -- compose FieldSet statement and return it
        return 'update ' || table_prefix || filterj.get(table_param).get_string || table_postfix ||
            ' set "' || updateField_str || '"=:data where rowid in (' || select_str || ')';
    end;

    function execute_set_field(update_str varchar2, in_data blob) return varchar2
    /*
        Executes one field set query "update_str" and sets it to in_data CLOB
    */
    is
        fetched number(20) := 0;
        datac clob;
        datalen number(20) := 0;
        pragma autonomous_transaction; -- allows DML operations inside a query

        function blob2clob(in_blob blob) return clob
        -- Converts BLOB in_blob to CLOB and return it
        is
            result           clob;
            loc_dest_offsset integer := 1;
            loc_src_offsset  integer := 1;
            loc_lang_context integer := dbms_lob.default_lang_ctx;
            loc_warning      integer := 0;
            loc_blob blob;
        begin
            if in_blob is null
            then
                -- nothing to do
                raise_application_error(-20001, '!!!!!!');
                return null;
            end if;
            --copy BLOB to another container, elsewhere it somewhy gets error ORA-22275
            --todo: try a different type of BLOB inside PHP to delete these two lines - done, failed
            --todo: advise something, please
            dbms_lob.createtemporary(loc_blob, true);
            dbms_lob.copy(loc_blob, in_blob, dbms_lob.getlength(in_blob), 1, 1);
            -- prepare CLOB
            dbms_lob.createTemporary(result, true);
            -- convert
            dbms_lob.converttoclob(
                dest_lob     => result,
                src_blob     => loc_blob,
                amount       => dbms_lob.lobmaxsize,
                dest_offset  => loc_dest_offsset,
                src_offset   => loc_src_offsset,
                blob_csid    => dbms_lob.default_csid,
                lang_context => loc_lang_context,
                warning      => loc_warning);
            return result;
        end;
    begin
        if update_str is null
        then
            -- nothing to do
            return '{rows:0}';
        end if;
        begin
            datalen := dbms_lob.getlength(in_data);
            if datalen = 0
            then
                -- just set an empty value
                execute immediate update_str
                    using in '';
            else
                -- try to set field of type BLOB
                execute immediate update_str
                    using in in_data;
            end if;
            fetched := sql%rowcount;
            commit;
        exception when others
        then
            -- ORA-00932: inconsistent datatypes, expected VARCHAR2, get BLOB
            if sqlcode = -932
            then
                begin
                    -- try to set field of type CLOB, VARCHAR2
                    datac := blob2clob(in_data);
                    execute immediate update_str
                        using in datac;
                    fetched := sql%rowcount;
                    commit;
                exception when others
                then
                    -- ORA-00932: inconsistent datatypes, expected NUMBER, get CLOB
                    if sqlcode = -932 and datalen<50
                    then
                        begin
                            -- try to set field of type NUMBER
                            execute immediate update_str
                                using in to_number(datac);
                            fetched := sql%rowcount;
                            commit;
                        exception when others
                        then
                            raise_application_error(-20001, update_str || ' ' || sqlerrm);
                        end;
                    else
                        --raise_application_error(-20001, update_str || ' ' || sqlerrm);
                        raise;
                    end if;
                end;
            else
                raise_application_error(-20001, update_str || ' ' || sqlerrm);
            end if;
        end;
        return '{rows:' || to_char(fetched) || ',bytes:' || datalen || '}';
    end;

    function set_field(in_filter_json varchar2, in_data blob) return varchar2
    /*
        Public function to set one field value from in_filter_json."table" equals to in_data value
        See detailed description in package specification
    */
    is
        filterj json;
    begin
        -- parse json string to json object
        filterj := parse_str_to_json(in_filter_json);
        return
            -- executes query, updating one field value
            execute_set_field(
                -- build query, based on json parameter
                prepare_set_field(filterj),
                -- data to set
                in_data
            );
    end;


END API;
/
