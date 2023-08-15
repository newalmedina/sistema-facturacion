# Usa la imagen oficial de PHP con Apache, versión 8.2
FROM php:8.2-apache

# Instala las dependencias necesarias y herramientas para Composer
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configura el servidor web de Apache
RUN a2enmod rewrite

# Copia los archivos de la aplicación al contenedor
COPY . /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia el archivo de configuración de ejemplo de Laravel
COPY .env.example .env

# Genera una clave de aplicación única
RUN php artisan cache:clear
RUN php artisan view:clear
RUN php artisan config:clear
RUN php artisan key:generate

# Instala las dependencias de Composer
RUN composer install --no-interaction --no-scripts --no-progress

# Establece los permisos adecuados en el directorio de almacenamiento
RUN chown -R www-data:www-data storage

# Expone el puerto 80
EXPOSE 80

# Inicia el servidor web de Apache
CMD ["apache2-foreground"]