# Docker CMIT

## 1 Descargar codigo fuente.
descarge el codigo de cmit o realice un git clone es esta carpeta.
```bash
git clone <codigo-cmit-repo> cmit
```

## 2 Configurar docker compose
copie `docker-compose.yml` y `config.sh` a la carpeta `cemit`.

## 3 ejecute el docker compose
dentro de la carpeta de cmit (usando bash en caso de linux o cmd en caso de linux) ejecute `docker compose up -d` para levantar los servicios

## 6 composer install
Tiene que tener en cuanta lo siguiente: los contenedores mariadb, redis y composer-aux con levantados antes que my-app. my-app depende de que composer-aux este levantado. el comando composer install es ejecutado cuando el contenedor es levantado. entonces tanto el comando composer install como my-app inician a la vez. es por esto que cuando finalice la instalacion de composer va a tener que reiniciar my-app.

## 7 configure el my-app
luego identifique la aplicacion en docker usando `docker ps` vera algo como esto
```bash
CONTAINER ID   IMAGE                  COMMAND                  CREATED         STATUS         PORTS                              NAMES
84b9e35bd071   bitnami/laravel:10     "/opt/bitnami/script…"   3 minutes ago   Up 3 minutes   3000/tcp, 0.0.0.0:8000->8000/tcp   docker-laravel-myapp-1
4614b680f375   bitnami/mariadb:11.0   "/opt/bitnami/script…"   3 minutes ago   Up 3 minutes   3306/tcp                           docker-laravel-mariadb-1
bd918170c7b3   redis                  "docker-entrypoint.s…"   3 minutes ago   Up 3 minutes   0.0.0.0:6382->6379/tcp             docker-laravel-redis-1
```

ejecute `docker exec -it docker-larave-myapp-1 bash` esto le abrira una terminal interactiva donde podra ejecutar el siguiente comando `./config.sh` esto iniciara la configuracion del proyecto.

sino desde docker desktop en `container>docker-laravel>my-app` en la seccion de terminal podra ejecute `bash` y luego .`/config.sh`.

luego de esto podra realizar cambios en la carpeta de cmit usando cualquier editor que prefiera y estos se reflejaran dentro del contenedor.