<img src="assets/images/logo.png" alt="CritiPixel" width="200" />

# CritiPixel

## Pré-requis

Pour une installation locale :
* PHP >= 8.2
* Composer
* Extension PHP Xdebug

## Installation


Dans un premier temps, installer les dépendances :
```bash
composer install
```

### Docker (optionnel)

Depuis la racine du projet, créez le fichier de variables Docker à partir de l'exemple.

Sous Windows avec PowerShell :
```powershell
Copy-Item .env.docker.example .env.docker
```

Sous Linux ou macOS :
```bash
cp .env.docker.example .env.docker
```

Modifiez `MYSQL_ROOT_PASSWORD` dans `.env.docker`, puis construisez et démarrez les conteneurs :
```bash
docker compose --env-file .env.docker up -d --build
```

Installez les dépendances, compilez les assets et initialisez la base de l'application :
```bash
docker compose --env-file .env.docker exec php composer install
docker compose --env-file .env.docker exec php composer build-assets
docker compose --env-file .env.docker exec php composer db
```

La compilation des assets produit des fichiers statiques dans `public/assets`, ce qui évite à Symfony de compiler les ressources à chaque chargement. Après chaque modification Sass, relancez `composer build-assets`.

L'application est disponible sur http://localhost:8000.

La base MySQL principale est persistée dans le volume Docker. Pour exécuter tous les tests, lancez :
```bash
docker compose --env-file .env.docker exec php composer test
```

Cette commande recrée la base de test séparée, applique les migrations, recharge les fixtures puis lance PHPUnit. Les données affichées par l'application ne sont pas modifiées.

Pour consulter les journaux du service PHP :
```bash
docker compose --env-file .env.docker logs php
```

Pour arrêter les services tout en conservant les données :
```bash
docker compose --env-file .env.docker down
```

Pour repartir d'un état vide, supprimez les volumes MySQL, Composer et cache Symfony, puis recommencez toute la procédure depuis `composer install` :
```bash
docker compose --env-file .env.docker down -v
```



## Configuration

### Base de données
Le fichier `.env` contient les paramètres par défaut de l'installation locale. Vous pouvez créer un fichier `.env.local` pour les adapter.

Exemple :
```dotenv
DATABASE_URL=mysql://root:Password123!@host:3306/criti-pixel
```

## Usage

### Base de données

#### Supprimer la base de données
```bash
symfony console doctrine:database:drop --force --if-exists
```

#### Créer la base de données
```bash
symfony console doctrine:database:create
```

#### Exécuter les migrations
```bash
symfony console doctrine:migrations:migrate -n
```

#### Charger les fixtures
```bash
symfony console doctrine:fixtures:load -n --purge-with-truncate
```

*Note : Vous pouvez exécuter ces commandes avec l'option `--env=test` pour les exécuter dans l'environnement de test.*

### SASS

#### Compiler les fichiers SASS
```bash
composer build-assets
```
*Note : le fichier `.symfony.local.yaml` peut surveiller les fichiers Sass avec le serveur local Symfony. Avec Docker, utilisez `composer build-assets` après chaque modification des styles.*

### Tests
```bash
composer test
```

Cette commande prépare automatiquement la base de test et ses fixtures avant d'exécuter PHPUnit.

### Serveur web
```bash
symfony serve
```