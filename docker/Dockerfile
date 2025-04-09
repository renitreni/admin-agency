# Base image
FROM php:8.3-fpm

# Install PHP extensions installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    libmemcached-tools \
    zlib1g-dev \
    libzstd-dev \
    libpq-dev \
    libsqlite3-dev \
    libpng-dev \
    libxml2-dev \
    libmagickwand-dev \
    libldap2-dev \
    libc-client-dev \
    libkrb5-dev \
    supervisor \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    unrar-free \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP build options
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl

# Install redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install memcached extension
RUN pecl install memcached && docker-php-ext-enable memcached

# Install pcov extension
RUN pecl install pcov && docker-php-ext-enable pcov

# Install all required PHP extensions
RUN install-php-extensions \
    exif \ 
    bcmath \
    curl \
    gd \
    imagick \
    imap \
    intl \
    ldap \
    mbstring \
    mysqli \
    opcache \
    pcntl \
    pdo_mysql \
    readline \
    soap \
    xml \
    zip

# Configure PHP settings
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=200M" > /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/upload-limit.ini \
    && echo "max_execution_time=120" > /usr/local/etc/php/conf.d/execution-time.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/storage/logs \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
