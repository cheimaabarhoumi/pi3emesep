package utils;

import java.io.IOException;

import Controllers.UpdateVoitureController;
import Controllers.VoitureDetailController;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;
import models.Voiture;

public class NavigationUtil {
    
    public static void navigateToVoitures() throws IOException {
        FXMLLoader loader = new FXMLLoader(NavigationUtil.class.getResource("/fxml/voiture.fxml"));
        Parent root = loader.load();
        Scene scene = new Scene(root);
        Stage stage = new Stage();
        stage.setScene(scene);
        stage.show();
    }
    
    public static void navigateToVoitureDetail(Voiture voiture) throws IOException {
        FXMLLoader loader = new FXMLLoader(NavigationUtil.class.getResource("/fxml/voiture-detail.fxml"));
        Parent root = loader.load();
        
        VoitureDetailController controller = loader.getController();
        controller.setVoiture(voiture);
        
        Scene scene = new Scene(root);
        Stage stage = new Stage();
        stage.setScene(scene);
        stage.show();
    }
    
    public static void navigateToUpdateVoiture(Voiture voiture) throws IOException {
        FXMLLoader loader = new FXMLLoader(NavigationUtil.class.getResource("/fxml/UpdateVoiture.fxml"));
        Parent root = loader.load();
        
        UpdateVoitureController controller = loader.getController();
        controller.setVoiture(voiture);
        
        Scene scene = new Scene(root);
        Stage stage = new Stage();
        stage.setScene(scene);
        stage.show();
    }
    
    // Close the current window
    public static void closeCurrentWindow(Parent root) {
        Stage stage = (Stage) root.getScene().getWindow();
        stage.close();
    }
}
