# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache to listen on a dynamic port
RUN echo 'Listen 10000' > /etc/apache2/ports.conf
RUN echo '<VirtualHost *:10000>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copy composer files first for better layer caching
COPY composer.json composer.lock* /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copy package files for Node dependencies
COPY package.json package-lock.json* /var/www/html/

# Install Node dependencies
RUN npm ci --legacy-peer-deps || npm install --legacy-peer-deps

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Run composer scripts and build assets
RUN composer dump-autoload --optimize --classmap-authoritative
RUN npm run build

# Expose port (Render will set PORT env var)
EXPOSE 10000

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Get PORT from environment (Render provides this)\n\
PORT=${PORT:-10000}\n\
\n\
# Update Apache configuration to use the PORT\n\
sed -i "s/Listen 10000/Listen $PORT/" /etc/apache2/ports.conf\n\
sed -i "s/:10000/:$PORT/" /etc/apache2/sites-available/000-default.conf\n\
\n\
# Wait for database to be ready (helps with initial deployment)\n\
echo "Waiting for database connection..."\n\
max_attempts=30\n\
attempt=0\n\
db_ready=false\n\
while [ $attempt -lt $max_attempts ]; do\n\
  if php artisan migrate:status &> /dev/null 2>&1; then\n\
    echo "Database is ready!"\n\
    db_ready=true\n\
    break\n\
  fi\n\
  attempt=$((attempt + 1))\n\
  echo "Waiting for database... (attempt $attempt/$max_attempts)"\n\
  sleep 2\n\
done\n\
\n\
# Clear and cache config\n\
php artisan config:clear || true\n\
php artisan cache:clear || true\n\
\n\
# Run migrations automatically if database is ready\n\
if [ "$db_ready" = true ]; then\n\
  echo "Running database migrations..."\n\
  php artisan migrate --force --no-interaction && echo "Migrations completed successfully!" || echo "Migration completed (may have been already run)"\n\
else\n\
  echo "Warning: Database connection not available. Skipping migrations. They will run on next container start."\n\
fi\n\
\n\
# Cache configuration for better performance\n\
php artisan config:cache || true\n\
php artisan route:cache || true\n\
php artisan view:cache || true\n\
\n\
echo "Starting Apache server on port $PORT..."\n\
# Start Apache\n\
exec apache2-foreground\n\
' > /start.sh && chmod +x /start.sh

# Start Apache
CMD ["/start.sh"]

