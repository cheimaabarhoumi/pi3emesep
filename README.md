# Mecharift

![Symfony](https://img.shields.io/badge/Symfony-6.x-black?logo=symfony) ![Build](https://img.shields.io/github/actions/workflow/status/cheimaabarhoumi/pidev3a12/ci.yml?branch=main) ![License](https://img.shields.io/github/license/cheimaabarhoumi/pidev3a12) ![Version](https://img.shields.io/badge/version-1.0.0-blue)

**Mecharift** est une application web développée avec le framework **Symfony**. Elle permet de gérer un système d’échange et de vente de voitures entre particuliers et professionnels. Les objectifs principaux de cette application sont de faciliter la mise en relation entre vendeurs et acheteurs, offrir un espace sécurisé et ergonomique pour publier, consulter et gérer des annonces de véhicules, et gérer les utilisateurs avec un système CRUD complet et des rôles différenciés (admin, utilisateur).

## Installation

1. Cloner le repository :  
   `git clone https://github.com/cheimaabarhoumi/pidev3a12.git`  
   `cd pidev3a12`

2. Installer les dépendances PHP via Composer :  
   `composer install`

3. Configurer l’environnement :  
   Dupliquer le fichier `.env` en `.env.local` et modifier les variables (base de données, mailer, etc.) selon votre environnement local.

4. Créer la base de données et exécuter les migrations :  
   `php bin/console doctrine:database:create`  
   `php bin/console doctrine:migrations:migrate`

5. Installer les assets frontend :  
   `npm install`  
   `npm run build`

6. Lancer le serveur Symfony :  
   `symfony server:start`

## Utilisation

Connectez-vous ou créez un compte utilisateur pour accéder aux fonctionnalités. Vous pouvez alors ajouter, modifier ou supprimer vos annonces de véhicules. Les administrateurs peuvent gérer les utilisateurs et les annonces via un dashboard sécurisé.

## Technologies utilisées

- Symfony 6.x
- Doctrine ORM
- Twig
- Bootstrap 5
- MySQL
- JavaScript (AJAX pour la recherche et les filtres)

## Exemple d’API

L’application expose certaines routes API RESTful. Exemple d’appel pour récupérer la liste des annonces :

`GET /api/annonces`  
Authorization: Bearer {token}  
Accept: application/json

Réponse :  
json
`[
  {
    "id": 1,
    "titre": "Renault Clio 2020",
    "prix": 12000,
    "kilometrage": 45000,
    "description": "Très bon état, révisions à jour"
  }
]`
## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet.
2. Créez une branche dédiée :
   ```bash
   git checkout -b feature/ma-nouvelle-fonctionnalite
   git commit -m "Ajout de ma nouvelle fonctionnalité"
   git push origin feature/ma-nouvelle-fonctionnalite
Auteur
👤 Habib Athimen
GitHub : HabibAthimen
Email : habibathimenn@gmail.com










