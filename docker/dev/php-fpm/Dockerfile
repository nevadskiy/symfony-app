FROM php:7.2-fpm

# Update dependencies
RUN apt-get update

# Install Postgres driver
RUN apt-get install -y libpq-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo_pgsql

WORKDIR /app
