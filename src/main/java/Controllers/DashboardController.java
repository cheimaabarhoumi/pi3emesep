package Controllers;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Button;
import javafx.scene.layout.StackPane;

public class DashboardController {
    @FXML
    private StackPane contentArea;
    
    @FXML
    private Button btnVoiture;
    
    @FXML
    private Button btnAnnonce;

    @FXML
    public void showVoitures() {
    System.out.println("[DEBUG] showVoitures() called, contentArea=" + (contentArea == null ? "null" : "ok"));
        try {
            Parent content = FXMLLoader.load(getClass().getResource("/fxml/voiture.fxml"));
            contentArea.getChildren().clear();
            contentArea.getChildren().add(content);
            
            btnVoiture.setStyle("-fx-background-color: #34495E; -fx-text-fill: white; -fx-alignment: CENTER_LEFT;");
            btnAnnonce.setStyle("-fx-background-color: transparent; -fx-text-fill: white; -fx-alignment: CENTER_LEFT;");
        } catch (Exception e) {
            e.printStackTrace();
            System.out.println("Error loading voiture view: " + e.getMessage());
        }
    }

    @FXML
    public void showAnnonces() {
        System.out.println("[DEBUG] showAnnonces() called, contentArea=" + (contentArea == null ? "null" : "ok"));
        try {
            Alert a = new Alert(AlertType.INFORMATION, "showAnnonces handler invoked");
            a.setHeaderText(null);
            a.show();
        } catch (Exception ignore) {
        }
        try {
            Parent content = FXMLLoader.load(getClass().getResource("/fxml/annonces-view.fxml"));
            contentArea.getChildren().clear();
            contentArea.getChildren().add(content);
            
            btnAnnonce.setStyle("-fx-background-color: #34495E; -fx-text-fill: white; -fx-alignment: CENTER_LEFT;");
            btnVoiture.setStyle("-fx-background-color: transparent; -fx-text-fill: white; -fx-alignment: CENTER_LEFT;");
        } catch (Exception e) {
            e.printStackTrace();
            System.out.println("Error loading annonce view: " + e.getMessage());
        }
    }

    @FXML
    public void initialize() {
        showVoitures();
    }
}
