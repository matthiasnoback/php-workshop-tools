FROM php:7.1-cli
COPY config/php/app.ini ${PHP_INI_DIR}/conf.d/20-app.ini
COPY script/* /usr/local/bin/
RUN chmod +x /usr/local/bin/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN install-xdebug
VOLUME /opt
WORKDIR /opt
ENTRYPOINT ["docker-entrypoint.sh"]
