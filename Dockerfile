FROM php:8.2-apache

# Install PostgreSQL and required extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean

# Copy all files to web root
COPY . /var/www/html/

# Enable Apache modules
RUN a2enmod rewrite

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Set Apache to use /var/www/html as root
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

EXPOSE 80