FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libgd-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl gd zip pdo pdo_mysql mysqli opcache mbstring \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --optimize-autoloader --no-scripts --no-interaction

EXPOSE 8080

CMD php spark migrate --no-interaction && php -S 0.0.0.0:$PORT -t public