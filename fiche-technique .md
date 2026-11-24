# Fiche technique

## 1. Présentation
Application web de gestion du patrimoine de l'État, en architecture micro-services.
Elle permet d'enregistrer, consulter, filtrer, modifier et supprimer les biens de l'État,
avec des statistiques et trois rôles d'utilisateurs.

## 2. Architecture

| Service | Port | Rôle |
|---|---|---|
| auth-service | 8000 | Connexion, jeton Sanctum, comptes et rôles |
| patrimoine-service | 8001 | Biens, catégories, statistiques |
| interface-web | 8002 | Pages web destinées aux utilisateurs |

- `interface-web` appelle `auth-service` pour la connexion et `patrimoine-service` pour les biens.
- `patrimoine-service` vérifie le jeton de l'utilisateur avant de répondre.
- Chaque service a sa propre base PostgreSQL : `base_auth` et `base_patrimoine`.

## 3. Technologies

| Élément | Technologie |
|---|---|
| Langage / framework | PHP, Laravel |
| Authentification | Laravel Sanctum (jetons) |
| Base de données | PostgreSQL 16 |
| Conteneurs | Docker, Docker Compose |
| Versionnement | Git, GitHub |

## 4. Sécurité
- Authentification par jeton Sanctum.
- Trois rôles : administrateur, responsable, agent, avec des droits différents.
- Vérification du jeton et du rôle dans `patrimoine-service`.
- Fichiers `.env` exclus de GitHub (`.gitignore`), modèles fournis dans `.env.example`.
- Mots de passe stockés chiffrés (hachés) en base.

## 5. Base de données

**base_auth** : table `users` (nom, e-mail, mot de passe haché, rôle, statut actif/inactif) et table des jetons Sanctum.

**base_patrimoine** : tables `categories` et `biens` (nom, catégorie, localisation, valeur, état, date d'acquisition).

## 6. Droits par rôle

| Action | Administrateur | Responsable | Agent |
|---|---|---|---|
| Consulter les biens et le tableau de bord | Oui | Oui | Oui |
| Ajouter un bien | Oui | Non | Oui |
| Modifier un bien | Oui | Oui | Oui |
| Supprimer un bien | Oui | Non | Non |
| Gérer les comptes | Oui | Non | Non |

## 7. Routes de l'API

Le préfixe `/api` est ajouté automatiquement par Laravel.

### auth-service (port 8000)

| Méthode | Route | Accès | Rôle |
|---|---|---|---|
| POST | /api/login | Public | Connexion, retourne un jeton |
| POST | /api/logout | Connecté | Déconnexion |
| GET | /api/user | Connecté | Utilisateur courant |
| GET | /api/comptes | Administrateur | Liste des comptes |
| POST | /api/comptes | Administrateur | Créer un compte |
| GET | /api/comptes/{id} | Administrateur | Détail d'un compte |
| PUT | /api/comptes/{id} | Administrateur | Modifier un compte |
| PATCH | /api/comptes/{id}/statut | Administrateur | Activer / désactiver |

### patrimoine-service (port 8001)

| Méthode | Route | Accès | Rôle |
|---|---|---|---|
| GET | /api/categories | Public | Liste des catégories |
| GET | /api/biens | Jeton | Tous les rôles |
| GET | /api/biens/{id} | Jeton | Tous les rôles |
| GET | /api/statistiques | Jeton | Tous les rôles |
| POST | /api/biens | Jeton | Administrateur, agent |
| PUT / PATCH | /api/biens/{id} | Jeton | Administrateur, agent, responsable |
| DELETE | /api/biens/{id} | Jeton | Administrateur |

Les contrôles se font par deux middlewares : `VerifierToken` (jeton valide) et
`VerifierRole` (rôle autorisé). Dans auth-service, `auth:sanctum` et
`EstAdministrateur` jouent le même rôle.

## 8. Déploiement
- En local : PostgreSQL via Docker Compose, services lancés avec `php artisan serve`.
- En ligne : hébergement gratuit (à compléter).