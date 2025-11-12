FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zlib1g-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip

# Enable mod_rewrite for clean URLs
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Enable PHP development error reporting
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php.ini

# Expose port
EXPOSE 80
RUN echo '==================================================================================' && \
    echo ' FXBG Closet Docker container is ready! ' && \
    echo '▶▶▶ Open: http://localhost:8000 ◀◀◀' && \
    echo '=================================================================================='


CMD ["apache2-foreground"]
