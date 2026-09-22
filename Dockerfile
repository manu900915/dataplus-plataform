FROM php:8.3-fpm

# Variables de entorno para directorios temporales
ENV TMPDIR=/tmp
ENV TEMP=/tmp
ENV TMP=/tmp

# Crear y fijar permisos de /tmp ANTES de cualquier otra cosa
RUN mkdir -p /tmp && chmod 1777 /tmp

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libldap2-dev \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install \
    ldap \
    pdo_pgsql \
    pgsql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    && docker-php-ext-enable intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Permisos de usuario
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Asegurar que /tmp sigue con buenos permisos al final
RUN chmod 1777 /tmp

RUN pecl install redis && docker-php-ext-enable redis

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
