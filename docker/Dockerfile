FROM php:7.4-fpm

ENV PATH="./vendor/bin:${PATH}" \
    NGINX_SERVER_NAME="_" \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="6000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="128"

RUN apt-get update && apt-get -y --no-install-recommends install \
    grep \
    git \
    default-mysql-client \
    procps \
    supervisor \
    sqlite3 \
    unzip \
    zip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgmp-dev \
    libicu-dev \
    libmagickwand-dev \
    libpq-dev \
    libvpx-dev \
    libxml2-dev \
    libxpm-dev \
    libwebp-dev \
    libzip-dev \
    libtool \
    nginx
#----------------#
# PHP EXTENSIONS #
#----------------#

# intl, zip, soap and opcache
RUN docker-php-ext-configure zip && docker-php-ext-install intl zip soap opcache

# mysqli, pdo, pdo_mysql, gmp
RUN docker-php-ext-install pgsql pdo_pgsql mysqli pdo pdo_mysql gmp bcmath exif

# gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install -j$(nproc) gd

# apcu
RUN docker-php-source extract && pecl install apcu-5.1.18 && docker-php-ext-enable apcu && docker-php-source delete

# igbinary
RUN docker-php-source extract && pecl install igbinary-3.1.2 \
    && docker-php-ext-enable igbinary && docker-php-source delete

# php-redis
RUN curl -fsSL 'https://github.com/phpredis/phpredis/archive/5.2.0.tar.gz' -o redis.tar.gz \
    && mkdir -p /tmp/redis \
    && tar -xf redis.tar.gz -C /tmp/redis --strip-components=1 \
    && rm redis.tar.gz \
    && docker-php-ext-configure /tmp/redis --enable-redis-igbinary \
    && docker-php-ext-install /tmp/redis \
    && rm -r /tmp/redis

# imagick
RUN docker-php-source extract && pecl install imagick-3.4.4 && docker-php-ext-enable imagick && docker-php-source delete

# xdebug
RUN docker-php-source extract && pecl install xdebug-2.9.2 && docker-php-ext-enable xdebug && docker-php-source delete

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#----------------#
#   CONF FILES   #
#----------------#

# php
COPY docker/php/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/php/conf.d/*.ini /usr/local/etc/php/conf.d/
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# nginx
COPY docker/nginx/h5bp /etc/nginx/h5bp

# supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/supervisor/conf.d/*.conf /etc/supervisor/conf.d-available/

# runn-app and confd
COPY docker/run-app.sh /usr/local/bin/run-app
ADD https://github.com/kelseyhightower/confd/releases/download/v0.16.0/confd-0.16.0-linux-amd64 /usr/local/bin/confd
COPY docker/confd/templates/ /etc/confd/templates/
COPY docker/confd/conf.d/ /etc/confd/conf.d/
RUN chmod +x /usr/local/bin/confd && chmod +x /usr/local/bin/run-app

#----------------#
#      EXEC      #
#----------------#

RUN usermod -u 1000 -s /bin/bash www-data && groupmod -g 1000 www-data

# app
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["/usr/local/bin/run-app"]
