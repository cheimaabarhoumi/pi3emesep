package Controllers;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class MainMenuController {

    @FXML
    void openEntretien(ActionEvent event) {
        try {
            Parent entretienView = FXMLLoader.load(getClass().getResource("/GestionEntretien.fxml"));
            Stage stage = (Stage) ((javafx.scene.Node) event.getSource()).getScene().getWindow();
            stage.setScene(new Scene(entretienView));
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @FXML
    void openRendezVous(ActionEvent event) {
        try {
            Parent rdvView = FXMLLoader.load(getClass().getResource("/GestionRDV.fxml"));
            Stage stage = (Stage) ((javafx.scene.Node) event.getSource()).getScene().getWindow();
            stage.setScene(new Scene(rdvView));
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
