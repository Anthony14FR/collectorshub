# 🚀 Getting Started - CollectorsHub

## Démarrage rapide (Docker)

### Prérequis
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installé et démarré
- Git

### Installation

```bash
# 1. Cloner le projet
git clone https://github.com/Anthony14fr/collectorshub
cd collectorshub

# 2. Lancer l'environnement complet
make docker-up-dev
```

**C'est tout !** 🎉

### Accès aux services

- 🌐 **Application** : http://localhost:8000
- ⚡ **Vite dev** : http://localhost:5173 (après `make docker-vite`)
- 📧 **Mailpit** (emails) : http://localhost:8025
- 🔴 **Redis** : localhost:6379

### Développement frontend

Pour le hot reload des assets Vue.js/CSS :
```bash
make docker-vite
```

### Commandes utiles

```bash
# Voir toutes les commandes disponibles
make help

# Voir les logs
make docker-logs

# Entrer dans le container
make docker-shell

# Arrêter les services
make docker-down

# Rebuild complet
make docker-rebuild
```

### Comptes de test

Après l'installation, vous avez accès à :
- **Admin** : admin@example.com / password
- **Utilisateur** : user@example.com / password

---

## Développement local (sans Docker)

Si vous préférez développer sans Docker :

```bash
# 1. Installer les dépendances
make setup

# 2. Démarrer les services
make start
```

---

**Questions ?** Consultez le [DOCKER_README.md](DOCKER_README.md) pour plus de détails. 