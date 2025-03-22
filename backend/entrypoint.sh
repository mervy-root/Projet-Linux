#!/bin/bash

# Attendre que MySQL soit prêt
echo "Attente de MySQL..."
until mysqladmin ping -h $DB_HOST --silent; do
  sleep 2
done

# Vérifier si la migration session existe déjà
if [ ! -f database/migrations/*_create_sessions_table.php ]; then
    echo "Création de la migration session..."
    php artisan session:table
fi

# Exécuter les migrations
echo "Exécution des migrations..."
php artisan migrate --force

# Démarrer le serveur Laravel
echo "Démarrage du serveur Laravel..."
php artisan serve --host=0.0.0.0 --port=8000

