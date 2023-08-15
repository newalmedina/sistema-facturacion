# Utilizamos la imagen oficial de PHP 8 con FPM
FROM php:8.0-fpm

# Instalamos las extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalamos las herramientas necesarias para MySQL
RUN apt-get update && apt-get install -y \
    mysql-client

# Configuramos el directorio de trabajo
WORKDIR /var/www

# Copiamos los archivos de tu proyecto Laravel al contenedor
COPY . /var/www

# Instalamos las dependencias de Composer
RUN composer install
RUN php artisan cache:clear
RUN php artisan view:clear
RUN php artisan config:clear

# Exponemos el puerto 9000 para el servidor PHP-FPM
EXPOSE 9000

# CMD ["php-fpm"]