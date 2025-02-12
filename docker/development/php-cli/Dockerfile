FROM php:8.4-cli-alpine

ENV XDEBUG_VERSION 3.4.1

RUN apk add --no-cache bash coreutils git unzip python3 py3-pip npm yarn make g++ linux-headers \
    libzip-dev libxml2-dev libpng-dev \
    postgresql-dev \
    && export EXTRA_CFLAGS="-I/usr/src/php" \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && git clone --branch $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-install pdo_pgsql xdebug \
    && docker-php-ext-install exif \
    && docker-php-ext-install zip xml gd simplexml pcntl bcmath

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

COPY ./development/php/conf.d /usr/local/etc/php/conf.d
COPY ./development/php-cli/conf.d /usr/local/etc/php/conf.d

ARG TZ=UTC
ENV TZ ${TZ}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /app
