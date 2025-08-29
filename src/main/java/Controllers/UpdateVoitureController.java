package Controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import models.Voiture;
import services.ServiceVoiture;

public class UpdateVoitureController {
    @FXML
    private TextField txtMarque;
    @FXML
    private TextField txtModele;
    @FXML
    private TextField txtAnnee;
    @FXML
    private TextField txtPrix;
    @FXML
    private TextField txtKilometrage;
    @FXML
    private Button btnUpdate;
    @FXML
    private Button btnCancel;

    private Voiture voiture;
    private final ServiceVoiture serviceVoiture = new ServiceVoiture();

    public void initialize() {
    }

    public void setVoiture(Voiture voiture) {
        this.voiture = voiture;
        populateFields();
    }

    private void populateFields() {
        if (voiture != null) {
            txtMarque.setText(voiture.getMarque());
            txtModele.setText(voiture.getModele());
            txtAnnee.setText(String.valueOf(voiture.getAnnee()));
            txtPrix.setText(String.valueOf(voiture.getPrix()));
            txtKilometrage.setText(String.valueOf(voiture.getKilometrage()));
        }
    }

    @FXML
    private void handleUpdate() {
        if (validateFields()) {
            voiture.setMarque(txtMarque.getText());
            voiture.setModele(txtModele.getText());
            voiture.setAnnee(Integer.parseInt(txtAnnee.getText()));
            voiture.setPrix(Double.parseDouble(txtPrix.getText()));
            voiture.setKilometrage(Integer.parseInt(txtKilometrage.getText()));

            try {
                serviceVoiture.update(voiture.getIdVoiture(), voiture);
                showAlert("Succès", "Modification réussie", "La voiture a été mise à jour avec succès.");
                closeWindow();
            } catch (Exception e) {
                showAlert("Erreur", "Erreur de mise à jour", "Une erreur est survenue lors de la mise à jour: " + e.getMessage());
            }
        }
    }

    @FXML
    private void handleCancel() {
        closeWindow();
    }

    private boolean validateFields() {
        if (txtMarque.getText().isEmpty() || txtModele.getText().isEmpty() || 
            txtAnnee.getText().isEmpty() || txtPrix.getText().isEmpty() || 
            txtKilometrage.getText().isEmpty()) {
            showAlert("Erreur", "Champs manquants", "Veuillez remplir tous les champs.");
            return false;
        }

        try {
            Integer.parseInt(txtAnnee.getText());
            Double.parseDouble(txtPrix.getText());
            Integer.parseInt(txtKilometrage.getText());
        } catch (NumberFormatException e) {
            showAlert("Erreur", "Format invalide", "Veuillez entrer des valeurs numériques valides pour l'année, le prix et le kilométrage.");
            return false;
        }

        return true;
    }

    private void showAlert(String title, String header, String content) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }

    private void closeWindow() {
        Stage stage = (Stage) btnCancel.getScene().getWindow();
        stage.close();
    }
}
