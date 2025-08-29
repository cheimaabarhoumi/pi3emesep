/*package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
          @Override
    public void start(Stage primaryStage) throws Exception {
        // Load the dashboard FXML
        FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/dashboard.fxml"));
        Scene scene = new Scene(loader.load());
        
        // Add CSS styling
        scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
        
        // Configure and show the stage
        primaryStage.setScene(scene);
        primaryStage.setTitle("MechaRift");
        primaryStage.setMaximized(true);
        primaryStage.show();           // Load FXML file
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/Messagerie.fxml"));

            // Create scene and set the VBox layout
            VBox root = loader.load(); // Use VBox instead of AnchorPane
            Scene scene = new Scene(root);

            // Set stage properties
            primaryStage.setTitle("Messagerie des Tickets");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}*/


/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {
        // Load the FXML file (adjust the path if needed)
        Parent root = FXMLLoader.load(getClass().getResource("/fxml/SponsoringView.fxml"));

        // Create a scene with the loaded root node
        Scene scene = new Scene(root, 800, 600);

        // Set the stage title and scene, then show the window
        primaryStage.setTitle("Gestion des Sponsorings");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}/*
/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.AnchorPane;
import javafx.stage.Stage;

import java.io.IOException;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/SuiviSponsoringView.fxml"));
            AnchorPane root = loader.load();
            Scene scene = new Scene(root);

            primaryStage.setTitle("Gestion des Suivis de Sponsoring");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement du fichier FXML");
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}

*/










/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {
    @Override
    public void start(Stage primaryStage) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/Tickets.fxml"));
            Parent root = loader.load();

            primaryStage.setTitle("Ticket Management");
            primaryStage.setScene(new Scene(root, 1124, 702));
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}
*/
/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.stage.Stage;
import utils.MyDb;

import java.io.IOException;
import java.net.URL;
import java.sql.Connection;

public class Main extends Application {
    private final MyDb database = MyDb.getInstance();
    private final Connection connection = database.getConnection();

    @Override
    public void start(Stage stage) {
        try {
            // Vérifier si le fichier FXML est bien accessible
            String fxmlPath = "fxml/voiture.fxml";
            URL fxmlLocation = getClass().getClassLoader().getResource(fxmlPath);

            if (fxmlLocation == null) {
                showErrorDialog("Fichier FXML introuvable", "Le fichier " + fxmlPath + " est introuvable. Vérifiez son emplacement.");
                System.err.println("❌ ERREUR : Fichier FXML non trouvé -> " + fxmlPath);
                return;
            } else {
                System.out.println("✅ Fichier FXML trouvé : " + fxmlLocation);
            }

            // Charger l'interface
            FXMLLoader fxmlLoader = new FXMLLoader(fxmlLocation);
            Parent root = fxmlLoader.load();

            // Afficher la scène
            Scene scene = new Scene(root, 800, 600); // Taille ajustée
            stage.setTitle("Gestion des Voitures - JavaFX");
            stage.setScene(scene);
            stage.show();

            System.out.println("🚀 Application JavaFX lancée avec succès !");
        } catch (IOException e) {
            e.printStackTrace();
            showErrorDialog("Erreur de chargement", "Impossible de charger l'interface : " + e.getMessage());
        }
    }

    private void showErrorDialog(String title, String message) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }

    public static void main(String[] args) {
        launch();
    }
}*/
/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import Controllers.NotificationService;
import models.RDV;

public class Main  extends Application {
    private final NotificationService notificationService = new NotificationService();
    RDV fakeRdv = new RDV();
    public static void main(String[] args) {
        launch(args);
    }

    @Override
    public void start(Stage stage) throws Exception{
        FXMLLoader fxmlLoader = new FXMLLoader(getClass().getResource("/Menu.fxml"));
        Parent root = fxmlLoader.load();
        Scene scene = new Scene(root);
        stage.setScene(scene);
        stage.setTitle("Gestion RDV");
        stage.setWidth(600);
        stage.show();
// Simuler un rendez-vous
        notificationService.start();
    }



    @Override
    public void stop() {
        notificationService.stop(); // Arrêter le service proprement
    }
}
*/

/*package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

import java.util.Objects;

public class Main extends Application {
    @Override
    public void start(Stage primaryStage) {
        try {
            // Charger la vue FXML contenant le contrôleur ListeContratController
            Parent root = FXMLLoader.load(Objects.requireNonNull(getClass().getResource("/fxml/ListeContratView.fxml")));
            Scene scene = new Scene(root);
            primaryStage.setTitle("Gestion des Contrats");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}


*/




package org.example;

import java.sql.Connection;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.stage.Stage;
import utils.MyDb;

public class Main extends Application {
    private final MyDb database = MyDb.getInstance();
    private final Connection connection = database.getConnection();

    @Override
    public void start(Stage primaryStage) throws Exception {
        // Load the dashboard FXML
        FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/dashboard.fxml"));
        Scene scene = new Scene(loader.load());
        
        // Add CSS styling
        scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());
        
        // Configure and show the stage
        primaryStage.setScene(scene);
        primaryStage.setTitle("MechaRift");
        primaryStage.setMaximized(true);
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}

/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.AnchorPane;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            // Charger le fichier FXML
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/PasswordReset.fxml"));
            GridPane gridPane = loader.load();  // Charger comme un GridPane
            Scene scene = new Scene(gridPane);   // Créer la scène avec le GridPane
            primaryStage.setScene(scene);        // Assigner la scène au stage
            primaryStage.show();




            // Définir le titre de la fenêtre
            primaryStage.setTitle("Réinitialiser le mot de passe");

            // Appliquer la scène à la fenêtre
            primaryStage.setScene(scene);

            // Afficher la fenêtre
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}
*/

/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import java.io.IOException;

public class Main extends Application {
    @Override
    public void start(Stage primaryStage) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/home.fxml"));
            VBox root = loader.load();
            Scene scene = new Scene(root, 600, 400); // Taille de la fenêtre

            primaryStage.setTitle("MecaRift - Home");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement de home.fxml !");
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}
/*



/*package org.example;

import javafx.stage.Stage;
import models.Prestataire;
import services.UsersService;
import javafx.application.Application;

public class Main extends Application {
    @Override
    public void start(Stage primaryStage) {
        try {
            UsersService usersService = new UsersService();
            Prestataire prestataire = new Prestataire(0, "cha", "chaa", "john.doe@example.com", "12345678", "password123", "Mécanicien", 50);
            usersService.create(prestataire);
            System.out.println("Prestataire ajouté !");
        } catch (Exception e) {
            e.printStackTrace();  // Affiche l'erreur dans la console
        }
    }
}

 */




//sign up
/*
package org.example;
import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import java.io.IOException;







import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;

import java.io.IOException;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/home.fxml"));
            BorderPane root = loader.load(); // Load as BorderPane instead of GridPane
            Scene scene = new Scene(root, 800, 800);
            primaryStage.setTitle("home");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}

/*
package org.example;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;

public class Main extends Application {

    @Override
    public void start(Stage primaryStage) {
        try {
            // Load the FXML file for the Login page
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/signup.fxml"));
            GridPane root = loader.load();  // Load as GridPane

            // Create and set the scene
            Scene scene = new Scene(root, 600, 600);
            primaryStage.setTitle("Login");
            primaryStage.setScene(scene);
            primaryStage.show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }


}
*/