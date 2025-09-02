# Mecharift

![JavaFX](https://img.shields.io/badge/JavaFX-17+-blue?logo=java) ![Build](https://img.shields.io/github/actions/workflow/status/HabibAthimen/MechaRift/build.yml?branch=main) ![License](https://img.shields.io/github/license/HabibAthimen/MechaRift) ![Version](https://img.shields.io/badge/version-1.0.0-blue)

**Mecharift** est une application bureautique développée avec **JavaFX**. Elle permet de gérer un système d’échange et de vente de voitures entre particuliers et professionnels. Les objectifs principaux de cette application sont de faciliter la mise en relation entre vendeurs et acheteurs, offrir une interface fluide et ergonomique pour publier, consulter et gérer des annonces de véhicules, et gérer les utilisateurs avec un système CRUD complet et des rôles différenciés (admin, utilisateur).

## Installation

1. Cloner le repository :  
   ```bash
   git clone https://github.com/HabibAthimen/MechaRift.git  
   cd MechaRift
   ```

2. Importer le projet dans votre IDE préféré (IntelliJ, Eclipse, NetBeans) avec le support JavaFX activé.

3. Configurer le JDK 17+ et JavaFX :  
   - Télécharger JavaFX SDK : https://gluonhq.com/products/javafx/  
   - Ajouter les librairies JavaFX à votre configuration de projet.

4. Si vous utilisez **Maven** ou **Gradle**, assurez-vous que les dépendances JavaFX sont bien intégrées dans le fichier `pom.xml` ou `build.gradle`.

## Utilisation

Lancez l'application via votre IDE ou avec une commande `java` si vous avez packagé l’application (`.jar`).  
Connectez-vous ou créez un compte utilisateur pour accéder aux fonctionnalités. Vous pouvez alors ajouter, modifier ou supprimer vos annonces de véhicules. Les administrateurs peuvent gérer les utilisateurs et les annonces via une interface dédiée.

## Technologies utilisées

- Java 17+
- JavaFX
- FXML
- SceneBuilder
- JDBC ou JPA (Hibernate)
- MySQL
- EmailJS (pour l'envoi d’e-mails)
- CSS (personnalisation de l’UI)

## Exemple de fonctionnalité

L'application permet par exemple d’envoyer un e-mail de confirmation à l’inscription :

```java
Email.send(
    "user@example.com",
    "Confirmation d'inscription",
    "Bienvenue sur MechaRift ! Votre compte a bien été créé."
);
```

## Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet.
2. Créez une branche dédiée :
   ```bash
   git checkout -b feature/ma-nouvelle-fonctionnalite
   git commit -m "Ajout de ma nouvelle fonctionnalité"
   git push origin feature/ma-nouvelle-fonctionnalite
   ```

## Auteur

👤 **Habib Athimen**  
GitHub : https://github.com/HabibAthimen  
Email : habibathimenn@gmail.com
