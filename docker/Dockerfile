FROM php:8.2-apache

# Installation des extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activation du module rewrite d'Apache
RUN a2enmod rewrite

# Configuration du répertoire de travail
WORKDIR /var/www/html

# Exposition du port 80
EXPOSE 80
