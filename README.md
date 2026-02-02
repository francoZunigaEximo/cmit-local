# Proyecto CMIT - Modulo Clientes

![Badge en Desarollo](https://img.shields.io/badge/STATUS-EN%20DESAROLLO-green)

## Información general
Proyecto de sistema preocupacional para Salud Ocupacional SRL. Modulo de altas, modificaciones y bajas de los pacientes del sistema.

## Consideraciones técnicas
* En el caso de ser una nueva instalación de esta rama, consulte la rama [master] y verifique la guía de instalación del README.md
```
https://github.com/nicolasEximo/cmit/tree/master
```


## Changelogs
[30/05/2023] Se procede a trabajar con Roles por necesidad de la solicitud de Asignado en CREATE y EDIT. Habilitar permisos de ejecución. No hay caso de uso de permisos, verificar mas adelante.
[31/05/2023] Crear Scripts de Estados para 'Pacientes' y 'Clientes'. Inserts de Users estandars. Indexacion en pacientes de Nombre, Apellido y Documento de Tabla Pacientes para optimización de busquedas.
[06/06/2023] Se realizan modificaciones en los modulos Pacientes y Clientes de dicha rama. La rama pacientes-abm estaria desactualizada hasta cargar los cambios a pedido de TEST.
[06/06/2023] Se carga el archivo scripts.sql con los cambios que se deben realizar en la base de datos para evitar problemas con NOT NULL en todas las entidades utilizadas.
[06/06/2023] IdAsignado no funciona hasta actualizar Perfile por Roles. Hacer los cambios en ClientesController una vez llegado el momento.

## Docker

Las imagenes que levantamos son 4; el servidor nginx que ejecutara la aplicacion, un servidor php-fpm para manajerar procesos de php, un workdir que sera quien genere una imagen con todas las librerias que necesitar el proyecto, y un servidor de notificaciones (es la misma imagen que el workdir, pero ejecuta el servidor reverb en un puerto especificado).

Cuando termine de levantar el proyecto recuerde llevarse las variable de entorno del archivo .env a el servidor para poder ejecutar la aplicacion.

### Pasos

1. ejecute `docker-compose up -d` 
2. en caso de no tener el en .env la clase APP_KEY o la tiene vacia ejecute `docker-compose exec workspace php artisan key:generate` .
3. instalar las dependencias con composer: `docker-compose exec workdir workspace composer install` .
4. pruebe navegando a `http://localhost:8084/` o `http://<ip-maquina>:8084/` .

### Info

- https://github.com/dockersamples/laravel-docker-examples/tree/main
- https://docs.docker.com/guides/frameworks/laravel/development-setup/#run-your-development-environment