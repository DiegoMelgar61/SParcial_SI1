# IMAGEN BASE OFICIAL DE PHP
FROM php:8.2-cli

# INSTALAR DEPENDENCIAS DEL SISTEMA Y EXTENSIONES DE PHP
RUN apt-get update && apt-get install -y \
    git unzip zip libpq-dev libzip-dev \
    libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev libxpm-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo pdo_pgsql gd zip

# DEFINIR DIRECTORIO DE TRABAJO
WORKDIR /app

# COPIAR TODO EL PROYECTO DENTRO DEL CONTENEDOR
COPY . /app

# INSTALAR COMPOSER (SI NO ESTÁ PRESENTE EN LA IMAGEN)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

# INSTALAR DEPENDENCIAS DEL PROYECTO
RUN composer install --no-dev --optimize-autoloader

# EXPONER EL PUERTO 8000 (USADO POR RENDER)
EXPOSE 8080

# COMANDO PARA INICIAR LA APLICACIÓN
CMD ["php", "run.php"]
