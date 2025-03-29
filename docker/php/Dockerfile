FROM php:8.1-cli

# Arguments de build
ARG USER_ID=1000
ARG GROUP_ID=1000

# Installation des dépendances et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    zip \
    intl \
    opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Installation de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configuration de Xdebug
RUN echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Création d'un utilisateur non-root avec les mêmes ID que l'utilisateur host
RUN groupadd -g ${GROUP_ID} appuser \
    && useradd -u ${USER_ID} -g appuser -m appuser

# Définition du répertoire de travail
WORKDIR /var/www/html

# Changer l'utilisateur
USER appuser

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]
