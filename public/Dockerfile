# Utilise une image PHP avec Apache (pour Laravel)
FROM php:8.2-apache

# Définit le répertoire de travail dans le conteneur
WORKDIR /var/www/html

# Installe les dépendances système nécessaires pour Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Active le module Apache rewrite (nécessaire pour Laravel)
RUN a2enmod rewrite

# Copie le fichier de configuration Apache pour Laravel
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copie les fichiers de ton projet Laravel dans le conteneur
COPY . .

# Installe Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installe les dépendances de Laravel avec Composer
RUN composer install --optimize-autoloader --no-dev

# Définit les permissions pour le dossier de stockage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose le port 80 (port par défaut d'Apache)
EXPOSE 80

# Commande pour démarrer Apache
CMD ["apache2-foreground"]
