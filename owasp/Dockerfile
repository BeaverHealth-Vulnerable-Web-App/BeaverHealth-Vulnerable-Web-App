# Use an official PHP image with Apache
FROM php:7.4-apache

# Copy the application files to the Apache web root directory
COPY . /var/www/html/

# Install necessary PHP extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Make necessary PHP configurations
RUN echo "allow_url_include = On" >> /usr/local/etc/php/php.ini
RUN echo "allow_url_fopen = On" >> /usr/local/etc/php/php.ini
RUN echo "safe_mode = Off" >> /usr/local/etc/php/php.ini

# Set permissions on the web root if needed
RUN chown -R www-data:www-data /var/www/html/

# Expose port 80 for the web server
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
