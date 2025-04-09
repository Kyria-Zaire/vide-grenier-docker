#!/bin/bash

# Arrêter les conteneurs existants
docker-compose -f docker-compose.prod.yml down

# Reconstruire et démarrer les conteneurs
docker-compose -f docker-compose.prod.yml up --build -d

# Afficher les logs
echo "Démarrage des conteneurs de production..."
docker-compose -f docker-compose.prod.yml logs -f
