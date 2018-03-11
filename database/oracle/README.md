Don't forget to learn php how-to connect to oracle database for [oci_pconnect](http://php.net/manual/ru/function.oci-pconnect.php) work.

If you have Oracle 12c or later, please, test package without PLJSON installation.

### Install Oracle PLJSON
1.  Download the latest release -- https://github.com/pljson/pljson/releases
2.  Extract the zip file
3.  Use `sql*plus`, or something capable of running `sql*plus` scripts, to
    run the `install.sql` script
4.  To test the implementation, run the `/testsuite/testall.sql` script

### Oracle database pooling
For speed up connecting to database, [oci_pconnect](http://php.net/manual/ru/function.oci-pconnect.php) used with pooling. You can stop/configure/start pooling from sqlplus
```sql
exec dbms_connection_pool.stop_pool();
exec dbms_connection_pool.alter_param('SYS_DEFAULT_CONNECTION_POOL','MAX_LIFETIME_SESSION','21600');
exec dbms_connection_pool.start_pool();
```

### Oracle database API views

By default, package adds "w_all." before table name and "#api" after table name
(see "table_prefix" and "table_postfix" variables in package specification)

You can do some specified for table calculations and additional inserts/updates in trigger for this view

Example:
```sql
CREATE OR REPLACE FORCE VIEW W_ALL.ACTION#API (ID, TYPE, DATE_IN, WEIGHT) AS
select id, type, date_in, weight from w_all.action;

CREATE OR REPLACE TRIGGER W_ALL.VII_ACTION#API INSTEAD OF INSERT ON W_ALL.ACTION#API FOR EACH ROW
begin
    insert into w_all.action (id, type, date_in, weight)
    values (:new.id, :new.type, nvl(:new.date_in,sysdate), :new.weight);
end;
```
