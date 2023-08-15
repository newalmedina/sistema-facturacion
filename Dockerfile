# Usa la imagen oficial de PHP con Apache, versión 8.2
FROM php:8.2-apache

# Instala las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Copia los archivos de la aplicación al contenedor
COPY . /var/www/html

# Configura el servidor web de Apache
RUN a2enmod rewrite

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Instala las dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Ejecuta Composer para instalar las dependencias de Laravel
RUN composer install

# Copia el archivo de configuración de ejemplo de Laravel
RUN cp .env.example .env

# Genera una clave de aplicación única
RUN php artisan key:generate
RUN php artisan cache:clear
RUN php artisan view:clear
RUN php artisan config:clear
# Establece los permisos adecuados en el directorio de almacenamiento
RUN chown -R www-data:www-data storage

# Expone el puerto 80
EXPOSE 8000

# Inicia el servidor web de Apache
CMD ["apache2-foreground"]