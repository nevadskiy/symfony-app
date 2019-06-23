FROM php:7.2-fpm

# Update dependencies
RUN apt-get update

# Install Postgres driver
RUN apt-get install -y libpq-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo_pgsql

# Xdebug
RUN pecl install xdebug-2.7.1
RUN docker-php-ext-enable xdebug

# Custom php.ini config
ADD ./php.ini /usr/local/etc/php/php.ini

WORKDIR /app
