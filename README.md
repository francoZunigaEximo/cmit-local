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

## Instalación
Los pasos para la instalación basica del proyecto es el siguiente:

```
$ git clone https://github.com/nicolasEximo/cmit.git
$ cd cmit
$ php artisan migrate
```
Utilice el [migrate] para instalar la tabla users en la base de datos seleccionada.

Edite el archivo [.env] para escribir los datos de su [localhost], [user], [password] y [port] sea cual fuere su servidor (Apache, Ngix, Lampp, Xampp, etc).

Nuevas tablas para funcionalidades de usuarios, roles y permisos de usuarios en el root del proyecto en el caso de no poder ejecutar las migraciones
* nuevas_tablas.sql 
