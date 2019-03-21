# REST API for Oracle

This API is used to access througth web-server to database, using REST metodology.

My goal is to refuse a heavy database client on controller side, replacing it by lightweight web-requests.

Programming way is Java-oriented OOP, including Dependency Injection


## Prerequisities

- PHP 5.5 or higher
- composer
- PHP database connection module


## Supported databases

- Oracle

The new databases is easy to add with new Model/OracleDB.php and Model/OracleStorage.php -like files and modify the Dependencies.php file. Suggest, I will add.

The database have to be with stored procedures!


### Request examples

```bash
$ curl http://dbuser:dbpass@localhost/dbtablename/4
$ curl http://dbuser:dbpass@localhost/dbtablename?TYPE=5&limit=2
$ curl http://dbuser:dbpass@localhost/dbtablename?TYPE=5&limit=2&fields=FIELDNAME1,FIELDNAME2
$ curl -i -X POST -H "Content-Type: application/json" -d '{"FIELDNAME1":"some text","FIELDNAME2":"0","FIELDNAME3":"1"}' http://dbuser:dbpass@localhost/dbtablename
$ curl -i -X POST -H "Content-Type: application/json" -d '{"FIELDNAME1":"some text","FIELDNAME2":0,"FIELDNAME3":1}' http://dbuser:dbpass@localhost/dbtablename
```
[see more examples](https://github.com/grigory-lobkov/php-db-rest-api/blob/master/EXAMPLES.md)

