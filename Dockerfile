FROM php:8.2-apache

WORKDIR /var/www/html

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    gnupg \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# NodeJS
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock /var/www/html
RUN composer install --optimize-autoloader

# Enable Apache rewrite module for URL rewriting
RUN a2enmod rewrite

# Replace default Apache config with Laravel-specific config
COPY apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# Set up the sail user
RUN groupadd -g 1000 sail && \
    useradd -u 1000 -g sail -m sail && \
    echo "sail:sail" | chpasswd \
    && usermod -a -G www-data sail

# Copy project directory and set access
COPY . /var/www/html
RUN chown -R sail:www-data /var/www/html
RUN chmod -R 775 /var/www/html

# Build assets
RUN npm install && npm run build

EXPOSE 80

CMD ["apache2-foreground"]
