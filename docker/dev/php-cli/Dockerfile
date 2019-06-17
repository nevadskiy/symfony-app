FROM php:7.2-cli

# Update dependencies
RUN apt-get update

# Install unzip
RUN apt-get install -y unzip

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /app
