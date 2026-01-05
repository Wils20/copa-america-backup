# Usamos PHP 8.2 con Apache
FROM php:8.2-apache

# 1. Instalar dependencias del sistema y extensiones PHP
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

# 2. Habilitar mod_rewrite de Apache (Vital para Laravel)
RUN a2enmod rewrite

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Instalar Node.js y NPM (Para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 5. Configurar el directorio de trabajo
WORKDIR /var/www/html

# 6. Copiar archivos del proyecto
COPY . .

# 7. Configurar permisos para Apache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Modificar la configuración de Apache para apuntar a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 9. Instalar dependencias de PHP y Node
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# 10. Comando por defecto (Inicia Apache)
CMD ["apache2-foreground"]
