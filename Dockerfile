FROM php:8.1-apache

# Autoriser Composer en root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    intl \
    zip \
    gd \
    fileinfo \
    && rm -rf /var/lib/apt/lists/*


# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Activer mod_rewrite
RUN a2enmod rewrite

# Définir le dossier de travail
WORKDIR /var/www/html

# Copier tout le projet
COPY . .

# Installer dépendances Laravel (SANS options dangereuses)
# RUN composer install --no-dev --optimize-autoloader
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs


# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Config Apache (public/)
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

EXPOSE 80

RUN php artisan config:clear \
    && php artisan cache:clear \
    && php artisan view:clear

CMD php artisan migrate --force && apache2-foreground

