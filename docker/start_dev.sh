#!/bin/bash

# Arrêter les conteneurs existants
docker-compose down

# Reconstruire et démarrer les conteneurs
docker-compose up --build -d

# Afficher les logs
docker-compose logs -f
