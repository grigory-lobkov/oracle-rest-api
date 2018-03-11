CREATE OR REPLACE
PACKAGE       API AUTHID CURRENT_USER AS
    /*
        Access to any table inside database

        Table name must start with "table_prefix" and finish with "table_postfix"
        Transport protocol: JSON
    */

    table_param varchar2(5) := 'table';         -- name of field with table name to access
    fields_param varchar2(6) := 'fields';       -- name of field to define result fields list
    order_param varchar2(5) := 'order';         -- name of field for order sequence
    offset_param varchar2(6) := 'offset';       -- name of field to skip number of rows
    limit_param varchar2(5) := 'limit';         -- name of field to limit number of resulted rows
    table_prefix varchar2(6) := 'w_all.';       -- add schema name first (include concating dot ".")
    table_postfix varchar2(4) := '#api';        -- to acces to view, not directly to the table


    function select_rows(in_filter_json varchar2) return clob;
    /*
        Selects some rows from "table"
        in_filter_json examples:
            {"table"="action","ID"=26477680}
            {table=action,DEV_ID=24,limit=1,offset=2}

        Execute from plsql examples:
            select w_sys.api.select_rows('{"table":"device"}') from dual
            select w_sys.api.select_rows('{"table":"device","fields":"ID,TYPE","ID":5}') from dual
            select w_sys.api.select_rows('{table:action,fields:"ID,WEIGHT",DEV_ID:24}') from dual
    */


    function insert_rows(in_source_str varchar2, in_data_json varchar2) return varchar2;
    /*
        Insert data "in_data_json" to table "in_source_str"
        in_source_str examples:
            action
            device
        in_data_json examples:
            {DEV_ID:24,WEIGHT:0,TYPE:1}
            {data:[{DEV_ID:12,ID:1},{ID:13},{DEV_ID:14}]}
            {names:[DEV_ID,"ID"],data:[[12,1],[13,2],[14,3]]}
    */


    function delete_rows(in_filter_json varchar2) return varchar2;
    /*
        Delete some rows from "table"
        All parameters for function select_rows is supported, except 'fields'

        in_filter_json examples:
            see select_rows examples, except 'fields' parameter
   */


    function update_rows(in_filter_json varchar2, in_data_json varchar2) return varchar2;
    /*
        Update some rows in in_filter_json."table"
        All updating fields goes in in_data_json

        in_filter_json examples:
            see select_rows examples, except 'fields' parameter
        in_data_json examples:
            {WEIGHT:1}
            {TYPE:1,DEV_ID:12}

        Execute from plsql examples:
            select w_sys.api.update_rows('{"table":"action","DEV_ID":24,"TYPE":3}','{TYPE:1}') from dual
    */


    function set_field(in_filter_json varchar2, in_data blob) return varchar2;
    /*
        Public function to set one field value from in_filter_json."table" equals to in_data value

        Execute from plsql examples:
            select w_sys.api.set_field('{table:action,DEV_ID:24,TYPE:2,fields:WEIGHT}','1200') from dual
    */

    function get_field(in_filter_json varchar2) return blob;
    /*
        Public function to get one field value from in_filter_json."table"

        Execute from plsql examples:
            select w_sys.api.get_field('{table:action,DEV_ID:24,TYPE:2,fields:WEIGHT}') from dual
    */


END API;
/
