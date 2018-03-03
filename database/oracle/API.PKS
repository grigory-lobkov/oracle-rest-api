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


    function select_rows(in_param_json varchar2) return clob;
    /*
        Selects some rows from "table"
        in_param_json examples:
            {"table"="action","ID"=26477680}
            {table=action,DEV_ID=24,limit=1,offset=2}

        Execute from plsql examples:
            select w_sys.api.select_rows('{"table":"device"}') from dual
            select w_sys.api.select_rows('{"table":"device","fields":"ID,TYPE","ID":5}') from dual
            select w_sys.api.select_rows('{table:action,fields:"ID,WEIGHT",DEV_ID:24}') from dual
    */


    function insert_rows(in_source_name varchar2, in_param_json varchar2) return varchar2;
    /*
        Insert data "in_param_json" to table "in_source_name"
        in_source_name examples:
            action
            device
        in_param_json examples:
            {DEV_ID:24,WEIGHT:0,TYPE:1}
            {data:[{DEV_ID:12,ID:1},{ID:13},{DEV_ID:14}]}
            {names:[DEV_ID,"ID"],data:[[12,1],[13,2],[14,3]]}
    */



END API;
/
