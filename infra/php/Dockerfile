FROM rocit/php:7.4 as php

ARG APP_USER_ID=1000

RUN usermod -u ${APP_USER_ID} app; \
    groupmod -g ${APP_USER_ID} app;

#---------------
# Composer
#---------------
FROM php as tools

USER root

# Composer
COPY --chown=app:app --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer selfupdate --2

USER app
