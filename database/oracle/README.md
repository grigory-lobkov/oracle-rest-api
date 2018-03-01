Don't forget to learn php how-to connect to oracle database for [oci_pconnect](http://php.net/manual/ru/function.oci-pconnect.php) work.

### Oracle database pooling
For speed up connecting to database, [oci_pconnect](http://php.net/manual/ru/function.oci-pconnect.php) used with pooling. You can stop/configure/start pooling from sqlplus
```sql
exec dbms_connection_pool.stop_pool();
exec dbms_connection_pool.alter_param('SYS_DEFAULT_CONNECTION_POOL','MAX_LIFETIME_SESSION','21600');
exec dbms_connection_pool.start_pool();
```
