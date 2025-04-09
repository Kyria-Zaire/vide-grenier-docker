# Vide Grenier Docker

Application web de gestion de vide grenier développée avec PHP, MySQL et Docker.

## Fonctionnalités

- ✨ Interface utilisateur moderne et responsive
- 📝 Ajout d'items avec nom, description et prix
- 🏷️ Système de catégories
- 🔍 Fonction de recherche
- ❌ Suppression d'items
- 💾 Persistance des données avec MySQL
- 🐳 Configuration Docker pour développement et production

## Prérequis

- Docker
- Docker Compose

## Installation

1. Cloner le repository :
```bash
git clone https://github.com/Kyria-Zaire/vide-grenier-docker.git
cd vide-grenier-docker
```

2. Lancer l'environnement de développement :
```bash
docker-compose up -d
```

3. Accéder à l'application :
- Développement : http://localhost:8082
- Production : http://localhost:8083

## Structure du Projet

```
vide-grenier-docker/
├── app/
│   └── index.php         # Application PHP
├── docker/
│   └── prod/
│       └── Dockerfile    # Configuration Docker pour la production
├── .env                  # Variables d'environnement
├── docker-compose.yml    # Configuration Docker Compose (dev)
└── docker-compose.prod.yml # Configuration Docker Compose (prod)
```

## Développement

L'environnement de développement utilise le port 8080 et monte les fichiers en temps réel pour le développement.

```bash
# Lancer l'environnement de développement
docker-compose up -d

# Voir les logs
docker-compose logs -f
```

## Production

L'environnement de production utilise le port 8081 et inclut des optimisations de sécurité.

```bash
# Lancer l'environnement de production
docker-compose -f docker-compose.prod.yml up -d
```

## Base de données

- Type : MySQL 8.0
- Base : cubes5_db
- Tables :
  - items (id, name, description, price, category_id, created_at)
  - categories (id, name)

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou un pull request.
