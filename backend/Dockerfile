FROM php:8.3-fpm

# Installer les extensions nécessaires pour Laravel
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git default-mysql-client && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www

# Copier le code de l'application dans le conteneur
COPY . .
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
# Installer les dépendances PHP
RUN composer install

RUN php artisan key:generate


# Exposer le port sur lequel Laravel écoute (par défaut, 8000)
EXPOSE 8000

# Lancer le serveur Laravel
CMD ["/entrypoint.sh"]
