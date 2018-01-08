# Build from official PHP image
FROM php:7.1-cli
COPY config/php/app.ini ${PHP_INI_DIR}/conf.d/20-app.ini

# Install Composer
COPY --from=composer:1.5 /usr/bin/composer /usr/local/bin/composer

# Update coreutils (to make `tail` work)
RUN apt-get update && apt-get install -y coreutils

# Install and configure XDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY config/php/xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.ini

# Enable signal handling
RUN docker-php-ext-install pcntl

# Prepare for mounting the project's code as a volume
VOLUME /opt
WORKDIR /opt

# Use a script as entrypoint
COPY script/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
