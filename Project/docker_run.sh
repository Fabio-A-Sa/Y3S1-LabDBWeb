#!/bin/bash
set -e

cd /var/www
env >> /var/www/.env
php artisan clear-compiled
php artisan config:clear
php-fpm8.1 -D
nginx -g "daemon off;"
