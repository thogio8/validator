FROM php:8.1-cli

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

# Configuration de Xdebug pour la couverture de code
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Définition du répertoire de travail
WORKDIR /var/www/html

# Commande par défaut pour exécuter les tests
CMD ["./vendor/bin/phpunit"]
