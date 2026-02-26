FROM php:8.2-cli-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    nodejs npm \
    sqlite \
    sqlite-dev \
    && docker-php-ext-install zip gd pdo pdo_sqlite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8050 5173

CMD ["/usr/local/bin/start.sh"]