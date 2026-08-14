# Use PHP 8.3 CLI as the base image
FROM php:8.3-cli

# Install system dependencies and PostgreSQL PHP extensions
RUN apt-get update -qq && apt-get install -y unzip git libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /app

# Copy Composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the rest of the application
COPY . .

# Set proper permissions for Laravel cache and storage
RUN chmod -R 775 storage bootstrap/cache

# Expose the port Render will use
EXPOSE 8000

# Run migrations, then start the Laravel built-in server
CMD sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"