FROM php:8.3-apache

# Install PostgreSQL libraries dan ekstensi PDO PostgreSQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan Apache mod_rewrite dan mod_headers untuk mendukung .htaccess
RUN a2enmod rewrite headers \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Direktori kerja web server
WORKDIR /var/www/html

# Salin seluruh file aplikasi
COPY . /var/www/html/

# Pastikan entrypoint bebas dari Windows CRLF dan executable
RUN sed -i 's/\r$//' /var/www/html/entrypoint.sh \
    && chmod +x /var/www/html/entrypoint.sh

# Atur izin direktori storage dan web root
RUN mkdir -p /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage

ENV PORT=80
EXPOSE 80

ENTRYPOINT ["/var/www/html/entrypoint.sh"]
