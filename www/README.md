# Contributing

PHP-DB-REST-API is licensed under the MIT License.


### Composer installation example (CentOS 6)

```bash
$ sudo yum install curl php
$ curl -sS https://getcomposer.org/installer | php
$ sudo mv composer.phar /usr/local/bin/composer
## from project folder:
$ composer require php-di/php-di
$ composer require doctrine/annotations
```


### Web server installation example (CentOS 6)

```bash
$ sudo yum install php-fpm php-opcache nginx
$ sudo service httpd disable
```

Make new config for server

```text
$ vi /etc/nginx/conf.d/db-api.conf

server {
        server_name api.mycompany.com;
        root /var/www/html;
        rewrite_log on;
        location / {
        rewrite "^/(*)" /index.php?table=$1 last;
        client_max_body_size    10m;    # max upload MB
        client_body_buffer_size 128k;
        index                   index.php;
        }
        location ~ \.php$ {
                include         /etc/nginx/fastcgi_params;
                fastcgi_pass    127.0.0.1:9000;
                fastcgi_index   index.php;
                fastcgi_param   SCRIPT_FILENAME /srv/api$fastcgi_script_name;
        }
}
```


### Runkit installation example (for development)

```bash
$ sudo yum install git
$ git clone https://github.com/zenovich/runkit.git
$ cd runkit
$ pecl install package.xml
```

Add lines to php.ini

```bash
$ vi /etc/php.ini
extension = runkit.so
runkit.internal_override = true
```


### Request examples

```bash
$ curl http://dbuser:dbpass@localhost/dbtablename?TYPE=5&limit=2
$ curl http://dbuser:dbpass@localhost/dbtablename?TYPE=5&limit=2&fields=FIELDNAME1,FIELDNAME2
$ curl -i -X POST -H "Content-Type: application/json" -d '{"FIELDNAME1":"some text","FIELDNAME2":"0","FIELDNAME3":"1"}' http://dbuser:dbpass@localhost/dbtablename
$ curl -i -X POST -H "Content-Type: application/json" -d '{"FIELDNAME1":"some text","FIELDNAME2":0,"FIELDNAME3":1}' http://dbuser:dbpass@localhost/dbtablename
```


### Feedback

Please, let me know, if there is some errors in this readme.