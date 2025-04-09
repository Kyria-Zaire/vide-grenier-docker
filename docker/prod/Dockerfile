FROM php:8.2-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Configuration de l'environnement de production
ENV PHP_ERROR_REPORTING=E_ALL & ~E_DEPRECATED & ~E_STRICT
ENV PHP_DISPLAY_ERRORS=Off
ENV PHP_LOG_ERRORS=On

# Copie des fichiers de l'application
COPY ../app/ /var/www/html/

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exposition du port 80
EXPOSE 80
