#!/bin/bash
set -e

cd /var/www/html

if [ ! -f composer.json ]; then
  echo "⚙️  No se encontró composer.json, creando nuevo proyecto Laravel..."
  rm -rf ./* .[^.]*
  composer create-project laravel/laravel .
else
  echo "✅ Proyecto Laravel detectado, saltando creación..."
fi

exec "$@"
