# Base builder image with all build dependencies (zip, composer, etc)
FROM php:7.2-fpm AS builder

# Update dependencies
RUN apt-get update

# Install unzip
RUN apt-get install -y unzip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1

# Set up working directory
WORKDIR /app

# Optimize copying project files (allow to avoid installing vendors if no changes in composer.lock were made)
COPY ./../../../composer.json ./
COPY ./../../../composer.lock ./

# Install vendors
RUN composer install --no-dev --prefer-dist --optimize-autoloader

###########################################################################################

# PHP-FPM build (separate build that does not have composer and only build dependencies)
FROM php:7.2-fpm

# Update dependencies
RUN apt-get update

# Install opcache
RUN docker-php-ext-install opcache

# Add PHP configuration file
COPY ./default.ini /usr/local/etc/php/conf.d/default.ini

# Set up working directory
WORKDIR /app

COPY --from=builder /app ./

# Copy all project files inside built image
COPY ./../../../ ./
