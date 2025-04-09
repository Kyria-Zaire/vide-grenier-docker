#!/bin/bash
echo "🚀 Démarrage de l'environnement de développement..."
docker-compose down
docker-compose up -d
echo "✅ Environnement de développement démarré sur http://localhost:8080"
echo "📝 Les logs sont accessibles avec : docker-compose logs -f"
