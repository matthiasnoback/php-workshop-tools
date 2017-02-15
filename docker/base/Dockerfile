# Build from official PHP image
FROM php:7.1-cli
COPY config/php/app.ini ${PHP_INI_DIR}/conf.d/20-app.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install and configure XDebug
RUN pecl install xdebug
COPY config/php/xdebug.ini ${PHP_INI_DIR}/conf.d/xdebug.ini

# Prepare for mounting the project's code as a volume
VOLUME /opt
WORKDIR /opt

# Use a script as entrypoint
COPY script/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
