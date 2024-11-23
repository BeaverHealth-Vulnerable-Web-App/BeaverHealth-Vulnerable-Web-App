FROM php:8.2-apache

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    curl \
    gnupg \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && node -v && npm -v

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the document root to Laravel's public directory
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Create the sail user
RUN groupadd -g 1000 sail && \
    useradd -u 1000 -g sail -m sail && \
    echo "sail:sail" | chpasswd

# Add sail user to www-data group
RUN usermod -a -G www-data sail

# Copy the application code
COPY . /var/www/html

# Change ownership to sail user and www-data group
RUN chown -R sail:www-data /var/www/html

# Switch to sail user
USER sail

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Clear and cache Laravel configuration
RUN php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Install npm dependencies and build
RUN npm install && \
    npm run build

# Switch back to root user
USER root

# Change ownership of storage and cache directories to www-data
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set appropriate permissions
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure application files are readable by www-data
RUN chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Run Apache
CMD ["apache2-foreground"]
