FROM php:8.0.8-apache

WORKDIR /backend

# Copy composer files to the working directory
COPY composer.json composer.lock ./

# Update the package lists and install necessary dependencies
RUN apt-get update && \
    apt-get install -y git zip unzip && \
    docker-php-ext-install pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Download and install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy all files to the working directory
COPY . .

# Generate optimized autoloader and install project dependencies
RUN composer dump-autoload --no-dev --optimize && composer install

# Copy Apache configuration file
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the ServerName to localhost in Apache configuration
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Unzip the data.zip file to data directory
RUN unzip -q data.zip -d . && rm data.zip

EXPOSE 80

# Start Apache in the foreground as the container's command
CMD ["apache2-foreground"]
