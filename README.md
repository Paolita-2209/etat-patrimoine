# Gestion du patrimoine de l'État

Application web de gestion du patrimoine de l'État, construite en
architecture micro-services avec Laravel, PostgreSQL et Docker.

Elle permet de gérer les biens de l'État (ajout, modification, suppression,
filtres, statistiques) avec trois rôles : administrateur, agent et responsable.

## Architecture

Le projet est composé de 3 applications Laravel indépendantes, qui communiquent entre elles :

| Service | Port | Rôle |
|---|---|---|
| auth-service | 8000 | Connexion, jeton Sanctum, gestion des rôles |
| patrimoine-service | 8001 | Biens, catégories, statistiques (vérifie le jeton) |
| interface-web | 8002 | Pages web (tableau de bord, biens, comptes) |

Base de données : PostgreSQL (Docker, port 5433) avec deux bases séparées :
`base_auth` et `base_patrimoine`.

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | admin@test.tg | Test1234! |
| Agent | agent@test.tg | Test1234! |
| Responsable | responsable@test.tg | Test1234! |

## Installation

### Prérequis
- PHP 8.2 ou plus, Composer
- Docker Desktop
- Git

### 1. Récupérer le projet
```bash
git clone https://github.com/Paolita-2209/etat-patrimoine.git
cd etat-patrimoine
```

### 2. Lancer PostgreSQL
```bash
docker compose up -d
```
Cela crée les bases `base_auth` et `base_patrimoine` (port 5433).

### 3. Configurer et lancer chaque service
Pour chacun des dossiers `auth-service`, `patrimoine-service` et `interface-web` :
```bash
cd auth-service
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```
(Pour `patrimoine-service`, lance aussi `php artisan db:seed` pour créer les catégories.)

### 4. Démarrer les 3 services (3 terminaux)
```bash
php artisan serve --port=8000   # dans auth-service
php artisan serve --port=8001   # dans patrimoine-service
php artisan serve --port=8002   # dans interface-web
```

L'application est accessible sur http://127.0.0.1:8002