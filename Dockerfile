FROM webdevops/php-apache:8.2

WORKDIR /app
COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --optimize-autoloader --no-scripts --no-interaction

ENV WEB_DOCUMENT_ROOT=/app/public

EXPOSE 80

CMD php spark migrate --no-interaction; supervisord