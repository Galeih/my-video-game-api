# My Video Game API (Without APIPlatform)

## Sommaire

- [Aperçu](#aperçu)
- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Utilisation](#utilisation)
- [Technologies utilisées](#technologies-utilisées)
- [Améliorations possibles](#améliorations-possibles)

## Aperçu

Ce projet est une API RESTful développée avec Symfony 7. Elle permet de gérer des informations sur des jeux vidéo et inclut :

## Fonctionnalités

* Un CRUD pour les entités suivantes :
  * VideoGame (title, releaseDate, description)
  * Category (name)
  * Editor (name, country)
  * User (avec gestion des rôles et authentification via JWT)
* Authentification par JWT (LexikJWTAuthenticationBundle) : Seul un utilisateur ayant le rôle ROLE_ADMIN peut créer, éditer ou supprimer des ressources.
* DataFixtures pour peupler la base de données avec des données de test.
* Documentation interactive de l'API générée par NelmioApiDocBundle accessible via Swagger UI.
* Gestion des erreurs, validation (via Symfony Validator) et pagination (exemple simplifié).

## Prérequis

* PHP 8.2 (ou supérieur)
* Composer
* Symfony CLI (facultatif mais recommandé)
* Un serveur de base de données (par exemple MariaDB ou MySQL)
* Un environnement de développement (Wamp, Xampp (quand il décide de fonctionner), LAMP, etc.)

## Installation

1. Cloner le dépot :

```bash
git clone https://github.com/votre-utilisateur/votre-depot.git
cd votre-depot
```

2. Installer les dépendances : 

```bash
composer install
```

3.  Configuration de l'environnement :
   
```bash
cp .env .env.local
```

4.  Créer la base de données :

```bash
php bin/console doctrine:database:create
```

5.  Exécuter les migrations :

```bash
php bin/console doctrine:migrations:migrate
```

6.  Charger les DataFixtures :

```bash
php bin/console doctrine:fixtures:load
```

7.  Générer les clés JWT :

```bash
php bin/console lexik:jwt:generate-keypair
```

8.  Démarrer le serveur Symfony :

```bash
symfony server:start
```

## Utilisation

1. Authentification
Envoyez une requête POST à /api/login_check avec le corps JSON suivant :
```bash
{
    "email": "admin@example.com",
    "password": "adminpass"
}
```

En cas de succès vous recevrez un token JWT à utiliser dans le header pour accéder aux routes protégées :

```bash
Authorization: Bearer VOTRE_TOKEN_JWT
```

2. Endpoints
   
Les endpoints de l'API sont préfixés par /api.
Par exemple :
  * VideoGame :
    - GET /api/videogames — Liste tous les jeux.
    - POST /api/videogames — Crée un nouveau jeu (uniquement pour ADMIN).
    - GET /api/videogames/{id} — Affiche un jeu.
    - PUT/PATCH /api/videogames/{id} — Met à jour un jeu (ADMIN).
    - DELETE /api/videogames/{id} — Supprime un jeu (ADMIN).
    
Category, Editor, User : De même, des endpoints CRUD sont disponibles.

3. Documentation interactive

La documentation de l'API est accessible via :

```bash
[http://localhost:3000](https://127.0.0.1:8000/api/doc)
```

## Commandes Utiles

Créer la base de données :
```bash
php bin/console doctrine:database:create
```

Exécuter les migrations :
```bash
php bin/console doctrine:migrations:migrate
```

Charger les fixtures :
```bash
php bin/console doctrine:fixtures:load
```

Générer les clés JWT :
```bash
php bin/console lexik:jwt:generate-keypair
```

Vider le cache :
```bash
php bin/console cache:clear
```

## Structure du projet

/config
Fichiers de configuration (routes, services, sécurité, etc.).

/src/Entity
Définition des entités : User, VideoGame, Category, Editor.

/src/Controller
Contrôleurs exposant les endpoints de l'API.

/src/Repository
Repositories pour interagir avec la base de données via Doctrine.

/src/DataFixtures
Fixtures pour peupler la base de données de données de test.

/public
Point d'entrée de l'application (index.php).
