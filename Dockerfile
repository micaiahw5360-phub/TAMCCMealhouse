FROM php:8.2-apache

# Copy all files
COPY . /var/www/html/

# Set proper directory index
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/directory-index.conf
RUN a2enconf directory-index

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html