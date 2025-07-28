FROM php:8.2-apache

# Argumentos para el usuario
ARG user
ARG uid

# Instalación de dependencias del sistema operativo y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    # Instalación de Node.js y npm
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Instalación de Composer (manejador de paquetes de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Creación de un usuario para no ejecutar todo como root
RUN useradd -G www-data,root -u ${uid} -d /home/${user} ${user}
RUN mkdir -p /home/${user}/.composer && \
    chown -R ${user}:${user} /home/${user}

# Configurar Apache DocumentRoot para Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establece el directorio de trabajo
WORKDIR /var/www

# Exponer el puerto 80
EXPOSE 80

USER ${user}