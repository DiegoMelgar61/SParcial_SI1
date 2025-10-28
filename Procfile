web: composer install --no-dev --optimize-autoloader && \
     npm install && \
     npm run build && \
     php artisan migrate --force && \
     php artisan config:cache && \
     php artisan route:cache && \
     php artisan view:cache && \
     php artisan serve --host 0.0.0.0 --port $PORT
