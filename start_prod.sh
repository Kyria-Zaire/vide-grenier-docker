#!/bin/bash
echo "🚀 Démarrage de l'environnement de production..."
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d --build
echo "✅ Environnement de production démarré sur http://localhost"
echo "📝 Les logs sont accessibles avec : docker-compose -f docker-compose.prod.yml logs -f"
