#!/bin/bash
echo "ingrese una app key:\n"
read key
echo "APP_KEY=$key" > .env

# php artisan migrate
php artisan key:generate