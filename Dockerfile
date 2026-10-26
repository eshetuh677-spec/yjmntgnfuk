# ============================================================
# EFPT_AMS - Dockerfile
# ============================================================

FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers

# Configure Apache
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html/EFPT_AMS

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/EFPT_AMS \
    && chmod -R 755 /var/www/html/EFPT_AMS \
    && chmod -R 777 /var/www/html/EFPT_AMS/uploads

# Configure PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Expose port 80
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
