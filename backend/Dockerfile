FROM php:8.2-fpm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    sockets \
    redis \
    zip \
    gd 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
CMD ["php-fpm"]