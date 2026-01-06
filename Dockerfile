FROM php:8.2-apache

# 1. Instalar dependencias y Supervisor
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 2. Habilitar módulos de Apache para Proxy y WebSockets
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel

# 3. Instalar Composer y Node
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

WORKDIR /var/www/html
COPY . .

# 4. Permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN mkdir -p /var/log/supervisor

# 5. Configurar Apache para que sea el Proxy de Reverb
# Redirige peticiones de WebSockets (WS) al puerto 8080 internamente
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    ProxyPass /app http://0.0.0.0:8080/app\n\
    ProxyPassReverse /app http://0.0.0.0:8080/app\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# 6. Build
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# 7. Copiar config de supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
