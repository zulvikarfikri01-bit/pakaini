#!/bin/sh
set -e

# Railway memberikan port aplikasi melalui environment variable PORT
PORT="${PORT:-80}"

echo "Starting Apache on port ${PORT}..."

# Sesuaikan port Apache dengan PORT dari Railway
sed -i "s/Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Jalankan Apache Web Server
exec apache2-foreground
