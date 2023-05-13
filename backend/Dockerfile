FROM php:8.0.8-apache

WORKDIR /backend

COPY composer.json composer.lock ./

RUN apt-get update && \
    apt-get install -y git zip unzip && \
    docker-php-ext-install pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --no-dev --optimize
RUN composer install

COPY apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Unzip the data.zip file to data directory
RUN unzip -q data.zip -d . && rm data.zip

EXPOSE 80

CMD ["apache2-foreground"]
