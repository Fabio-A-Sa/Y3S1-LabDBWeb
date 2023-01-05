FROM ubuntu:22.04

# Install dependencies
env DEBIAN_FRONTEND=noninteractive
RUN apt-get update
RUN apt-get install -y --no-install-recommends libpq-dev vim nginx php8.1-fpm php8.1-mbstring php8.1-xml php8.1-pgsql php8.1-curl

# Copy project code and install project dependencies
COPY --chown=www-data . /var/www/

# Copy project configurations
COPY ./etc/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./etc/nginx/default.conf /etc/nginx/sites-enabled/default
COPY .env_production /var/www/.env
COPY docker_run.sh /docker_run.sh

# Start command
CMD sh /docker_run.sh
