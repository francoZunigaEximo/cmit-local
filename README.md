# Proyecto CMIT

![Badge en Desarollo](https://img.shields.io/badge/STATUS-EN%20DESAROLLO-green)

## Información general
Proyecto de sistema preocupacional para Salud Ocupacional SRL

## Tecnologías
Las lista de tecnologías utilizadas en el proyecto son:
* [PHP] - Versión: 8.x 
* [MySQL] - Versión: 2.4.x 
* [Jquery] - Versión: 3.6.x 
* [Bootstrap] - Versión: 5.x
* [Laravel] - Versión: 10.x

* Servidor: Lampp (Linux) / Xampp (Windows) / AppServ/ Laragon
-> Lampp y Xampp: https://www.apachefriends.org/download.html
-> AppServ: https://www.appserv.org/en/
-> Laragon: https://laragon.org/ 

## Instalación
Los pasos para la instalación basica del proyecto es el siguiente:

* Descargamos el proyecto
```
$ git clone https://github.com/nicolasEximo/cmit.git
$ cd cmit
```

* Descargar composer para administrar dependencias, autoloading y scripts personalizados. https://getcomposer.org
* En la carpeta cmit, actualizar las dependencias y descargar el vendor. Ejecutamos el composer UPDATE en la consola:
```
$ composer update
```

* Limpiamos la cache del proyecto
```
$ php artisan cache:clear
$ composer dump-autoload
```

* Creamos un archivo .env para las variables de entorno y poder configurar la conexión a la base de datos
```
$ touch .env
```

* Abrimos el archivo .env y copiamos el siguiente código
`APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:xzcbDXPiv10GQui0vxHzmJb8IvLLQ8u/ewjYzF1+3dA=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=TU_HOST
DB_PORT=3306
DB_DATABASE=NOMBRE_BD
DB_USERNAME=USUARIO_BD
DB_PASSWORD=CONTRASEÑA_BD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
`

* Debe modificar los siguientes valores con los de su base de datos para que funcione:
`
DB_HOST=TU_HOST
DB_PORT=3306
DB_DATABASE=NOMBRE_BD
DB_USERNAME=USUARIO_BD
DB_PASSWORD=CONTRASEÑA_BD
`

* Una ver guardados los cambios, actualize la key de laravel para encriptar datos sensibles y generar firmas de seguridad.
```
php artisan key:generate
```
* En el root del proyecto descargamos el archivo [nuevas_tablas.sql] en donde se encuentran las entidades basicas del proyecto y las nueva entidad [users].

* En el caso de no tener la entidad [users] realice un migrate para poder utilizarlas con el sistema.
```
php artisan migrate
```
El proyecto tendria que funcionar sin problemas.
El root del proyecto es http://localhost/cmit/public/

En el caso de que el archivo .htaccess no le redireccione de manera correcta a la carpeta [public] o tiene inconvenientes dependendo de si su servidor es una distro Linux o Windows, cree en el root un archivo index.php y agregue el siguiente codigo
`
<?php
 header('Location: http://localhost/cmit/public/index.php');
?>
` 
Nota: En el caso de usar servidores LINUX, recuerde dar los permisos de lectura y escritura correspondientes.
```
$ sudo chmod 777 -R storage && sudo chmod 777 -R bootstrap
```


Enjoy!
