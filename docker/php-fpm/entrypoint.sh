#!/bin/bash
set -e

# Check if $UID and $GID are set, else fallback to default (1000:1000)
USER_ID=${UID:-1000}
GROUP_ID=${GID:-1000}

# Clear configurations to avoid caching issues in development
cd /var/www

echo "Clearing configurations..."
# Verificamos si artisan existe antes de ejecutar nada
if [ -f "artisan" ]; then
    php artisan config:clear || echo "Warning: config:clear failed"
    php artisan route:clear || echo "Warning: route:clear failed"
    php artisan view:clear || echo "Warning: view:clear failed"
else
    echo "ERROR: 'artisan' file not found in $(pwd)!"
    echo "Listing current directory content:"
    ls -la
fi

# Run the default command (e.g., php-fpm or bash)
exec "$@"