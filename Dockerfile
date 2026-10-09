FROM php:8.3-apache

# Install PostgreSQL libraries dan dependensi PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan Apache mod_rewrite dan mod_headers untuk mendukung .htaccess
RUN a2enmod rewrite headers

# Direktori kerja web server
WORKDIR /var/www/html

# Salin seluruh file aplikasi ke dalam container
COPY . /var/www/html/

# Buat direktori storage dan atur izin akses file
RUN mkdir -p /var/www/html/storage \
    && chmod +x /var/www/html/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage

# Port default (dapat ditimpa oleh Railway via $PORT)
ENV PORT=80
EXPOSE 80

# Jalankan entrypoint script untuk binding port dinamis Railway
ENTRYPOINT ["/var/www/html/entrypoint.sh"]
