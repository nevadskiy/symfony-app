FROM php:7.2-cli

# Update dependencies
RUN apt-get update

# Install unzip
RUN apt-get install -y unzip

# Add PHP configuration file
COPY ./default.ini /usr/local/etc/php/conf.d/default.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1

# Set up working directory
WORKDIR /app

# Optimize copying project files (allow to avoid installing vendors if no changes in composer.lock were made)
COPY ./../../../composer.json ./
COPY ./../../../composer.lock ./

# Install vendors
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts

# Copy all project files inside built image
COPY ./../../../ ./
