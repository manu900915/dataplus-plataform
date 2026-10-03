FROM registry.docker.ir/php:8.3-fpm

# Variables de entorno para directorios temporales
ENV TMPDIR=/tmp \
    TEMP=/tmp \
    TMP=/tmp

WORKDIR /var/www

# Capa única: dependencias del sistema + extensiones PHP + Redis (PECL) + Node.js
RUN mkdir -p /tmp && chmod 1777 /tmp \
    && apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libzip-dev \
        zip \
        unzip \
        git \
        curl \
        ca-certificates \
        gnupg \
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
    # Extensión redis vía PECL
    && pecl install redis \
    && docker-php-ext-enable redis \
    # Verificación en tiempo de build: la extensión ldap DEBE estar cargada.
    # Si no, el build falla aquí (no en producción con "ldap_escape undefined").
    && php -m | grep -qi '^ldap$' \
    && php -r 'exit(function_exists("ldap_escape") ? 0 : 1);' \
    # Node.js 20.x en la misma capa (evita un segundo apt-get update)
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    # Limpieza al final para reducir tamaño de imagen
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=registry.docker.ir/composer:latest /usr/bin/composer /usr/bin/composer

# Permisos de usuario + asegurar permisos de /tmp
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data \
    && chmod 1777 /tmp

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
