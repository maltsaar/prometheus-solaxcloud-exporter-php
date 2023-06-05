FROM trafex/php-nginx:latest

COPY config/conf.d /etc/nginx/conf.d/

RUN rm /var/www/html/*

COPY src/* /var/www/html

# Install composer from the official image
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Run composer install to install the dependencies
RUN composer install --optimize-autoloader --no-interaction --no-progress