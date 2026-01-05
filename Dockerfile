# Utiliser PHP 8.1 avec Apache
FROM php:8.1-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer.json et composer.lock en premier (pour cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances Laravel avec limite mémoire augmentée
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --optimize-autoloader \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

# Copier le reste des fichiers du projet
COPY . /var/www/html

# Générer l'autoloader optimisé
RUN composer dump-autoload --optimize --no-dev

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurer Apache directement
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Activer mod_rewrite
RUN a2enmod rewrite

# Exposer le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]
