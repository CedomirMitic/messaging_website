FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# FIX PERMISSIONS: These must run BEFORE the CMD
# Create the livewire-tmp directory just in case
RUN mkdir -p /var/www/storage/app/public/livewire-tmp
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear
# Setup Nginx
# Note: Ensure your nginx.conf is configured to listen on 8080 as well
COPY .fly/nginx.conf /etc/nginx/sites-available/default

EXPOSE 8080

# Use a script or simply artisan serve if you aren't ready for Nginx + FPM setup
# But moved to the bottom so all RUN commands finish first
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]