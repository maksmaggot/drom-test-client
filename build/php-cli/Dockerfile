FROM php:7.3-cli

ENV ACCEPT_EULA=Y

RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        zip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./ /var/www/

WORKDIR /var/www
RUN composer install --no-interaction --no-autoloader --no-suggest --prefer-dist
RUN composer dump-autoload --no-interaction --optimize

RUN rm -rf /var/www/build \
    #for tests complition sabotage
    && touch /var/www/vendor/phpunit/phpunit/.phpunit.result.cache \
    && chmod 766 /var/www/vendor/phpunit/phpunit/.phpunit.result.cache \
    && chmod a-rwx /var/www/testdata/locked \