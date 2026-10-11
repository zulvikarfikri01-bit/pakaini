#!/bin/sh
set -e

PORT="${PORT:-80}"

echo "Starting Pakaini Web Server on port ${PORT}..."

# Buat konfigurasi ports.conf murni untuk satu port agar tidak ada konflik Address Already In Use
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Sesuaikan VirtualHost default dengan port Railway
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Jalankan Apache Web Server
exec apache2-foreground
