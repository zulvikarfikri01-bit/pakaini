#!/bin/sh
set -e

PORT="${PORT:-80}"

echo "Starting Pakaini Web Server (Listening on port 80 and ${PORT})..."

# Pastikan Apache mendengarkan port 80 DAN port dinamis Railway ($PORT)
# Ini mencegah masalah port mismatch jika Railway mengarahkan trafik ke port 80 atau $PORT
if [ -n "${PORT}" ] && [ "${PORT}" != "80" ]; then
    cat <<EOF > /etc/apache2/ports.conf
Listen 80
Listen ${PORT}
EOF
else
    echo "Listen 80" > /etc/apache2/ports.conf
fi

# Konfigurasi VirtualHost bersih yang melayani semua port (*:*)
cat <<EOF > /etc/apache2/sites-available/000-default.conf
<VirtualHost *:*>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    <Directory /var/www/html>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Hilangkan peringatan FQDN Apache
echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf
a2enconf servername > /dev/null 2>&1 || true

# Teruskan Environment Variables Railway (DATABASE_URL, dll.) ke proses Apache & PHP
cat <<EOF > /etc/apache2/conf-available/railway-env.conf
PassEnv DATABASE_URL
PassEnv PORT
PassEnv DB_HOST
PassEnv DB_PORT
PassEnv DB_NAME
PassEnv DB_USER
PassEnv DB_PASS
EOF
a2enconf railway-env > /dev/null 2>&1 || true

# Tulis file .env runtime agar dapat dibaca langsung oleh PHP
if [ -n "${DATABASE_URL}" ]; then
    echo "DATABASE_URL=${DATABASE_URL}" > /var/www/html/.env
    chmod 644 /var/www/html/.env
fi

# Jalankan Apache Web Server
exec apache2-foreground
