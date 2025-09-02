FROM php:8.2-fpm-alpine

RUN apk add --no-cache bash autoconf g++ make unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv || true \
    && docker-php-ext-enable pdo_sqlsrv sqlsrv || true \
    && apk add --no-cache icu-dev libxml2-dev oniguruma-dev

WORKDIR /var/www/html

COPY --chown=www-data:www-data . /var/www/html

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

EXPOSE 9000

CMD ["php-fpm"]
