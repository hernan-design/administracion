FROM php:8.0-apache

# 1. Instalar dependencias del sistema y herramientas de compilación
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libssl-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Configurar e instalar extensiones de PHP comunes para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        soap \
        bcmath \
        exif \
        pdo_pgsql \
        pgsql

# 3. Instalar extensión de MongoDB para PHP mediante PECL
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# 4. Habilitar mod_rewrite de Apache para soportar el enrutamiento de Laravel
RUN a2enmod rewrite

# 5. Redireccionar el directorio raíz (DocumentRoot) de Apache hacia /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Establecer el directorio de trabajo
WORKDIR /var/www/html

# Puerto expuesto por Apache por defecto
EXPOSE 80
