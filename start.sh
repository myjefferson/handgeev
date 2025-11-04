#!/bin/bash

echo "🚀 Starting Laravel server and queue worker..."

# Inicia o servidor Laravel em segundo plano
php artisan serve --host=0.0.0.0 --port=${PORT} &

# Inicia o worker em primeiro plano
php artisan queue:work --sleep=3 --tries=3 --timeout=90
