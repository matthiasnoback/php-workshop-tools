# Build from official PHP image
FROM php:7.1-cli
COPY config/php/app.ini ${PHP_INI_DIR}/conf.d/20-app.ini

# Install Composer
RUN DEBIAN_FRONTEND=noninteractive && \
    apt-get update && \
    apt-get install -y git zlib1g-dev && \
    docker-php-ext-install -j$(nproc) zip && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    chmod +x composer.phar && \
    mv composer.phar /usr/local/bin/composer

# Install and configure XDebug
RUN pecl install xdebug
COPY config/php/xdebug.ini ${PHP_INI_DIR}/conf.d/ext-xdebug.ini

# Prepare for mounting the project's code as a volume
VOLUME /opt
WORKDIR /opt

# Use a script as entrypoint
COPY script/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
