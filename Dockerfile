# Dockerfile
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/symfony

# Copy existing project files (can be skipped because we use volume mount in docker-compose)
# COPY . .

# Symfony CLI (optional, for `symfony serve`)
# RUN curl -sS https://get.symfony.com/cli/installer | bash && \
#     mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
