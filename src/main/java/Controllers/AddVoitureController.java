package Controllers;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;

import javafx.collections.FXCollections;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.stage.FileChooser;
import javafx.stage.Stage;
import models.Voiture;
import services.ServiceVoiture;

public class AddVoitureController {
    @FXML private ComboBox<String> comboMarque;
    @FXML private ComboBox<String> comboModele;
    @FXML private TextField txtAnnee;
    @FXML private TextField txtPrix;
    @FXML private TextField txtPuissanceFiscale;
    @FXML private TextField txtKilometrage;
    @FXML private ComboBox<String> comboBoiteVitesse;
    @FXML private ComboBox<String> comboCarburant;
    @FXML private TextField txtCylindree;
    @FXML private TextField txtNombrePortes;
    @FXML private Button btnChooseImage;
    @FXML private ImageView imageViewVoiture;
    
    private File selectedImageFile;
    private ServiceVoiture serviceVoiture = new ServiceVoiture();

    @FXML
    public void initialize() {
        comboMarque.setItems(FXCollections.observableArrayList(
            "Audi", "BMW", "Mercedes", "Toyota", "Honda", "Ford", "Volkswagen"
        ));
        
        comboBoiteVitesse.setItems(FXCollections.observableArrayList(
            "Manuelle", "Automatique"
        ));
        
        comboCarburant.setItems(FXCollections.observableArrayList(
            "Essence", "Diesel", "Hybride", "Électrique"
        ));

        txtAnnee.textProperty().addListener((obs, oldVal, newVal) -> {
            if (!newVal.matches("\\d*")) {
                txtAnnee.setText(newVal.replaceAll("[^\\d]", ""));
            }
        });

        txtPrix.textProperty().addListener((obs, oldVal, newVal) -> {
            if (!newVal.matches("\\d*\\.?\\d*")) {
                txtPrix.setText(oldVal);
            }
        });

        comboMarque.setOnAction(e -> updateModels());
    }

    private void updateModels() {
        String marque = comboMarque.getValue();
        if (marque != null) {
            switch (marque) {
                case "Audi":
                    comboModele.setItems(FXCollections.observableArrayList("A3", "A4", "A6", "Q5"));
                    break;
                case "BMW":
                    comboModele.setItems(FXCollections.observableArrayList("Série 3", "Série 5", "X3", "X5"));
                    break;
                case "Mercedes":
                    comboModele.setItems(FXCollections.observableArrayList("Classe C", "Classe E", "GLC", "GLE"));
                    break;
                default:
                    comboModele.setItems(FXCollections.observableArrayList());
            }
        }
    }

    @FXML
    private void handleChooseImage() {
        FileChooser fileChooser = new FileChooser();
        fileChooser.setTitle("Choisir une image");
        fileChooser.getExtensionFilters().add(
            new FileChooser.ExtensionFilter("Images", "*.png", "*.jpg", "*.jpeg")
        );
        
        File file = fileChooser.showOpenDialog(btnChooseImage.getScene().getWindow());
        if (file != null) {
            selectedImageFile = file;
            Image image = new Image(file.toURI().toString());
            imageViewVoiture.setImage(image);
        }
    }

    @FXML
    private void handleAdd() {
        if (!validateInputs()) {
            return;
        }

        try {
            String imagePath = selectedImageFile != null ? saveImage(selectedImageFile) : "default.jpg";
            
            Voiture voiture = new Voiture(
                comboMarque.getValue(),
                comboModele.getValue(),
                Integer.parseInt(txtAnnee.getText()),
                Double.parseDouble(txtPrix.getText()),
                Integer.parseInt(txtPuissanceFiscale.getText()),
                Integer.parseInt(txtKilometrage.getText()),
                comboBoiteVitesse.getValue(),
                comboCarburant.getValue(),
                txtCylindree.getText(),
                Integer.parseInt(txtNombrePortes.getText()),
                imagePath
            );

            serviceVoiture.create(voiture);
            showAlert(Alert.AlertType.INFORMATION, "Succès", "La voiture a été ajoutée avec succès!");
            
            clearForm();
        } catch (Exception e) {
            showAlert(Alert.AlertType.ERROR, "Erreur", "Erreur lors de l'ajout de la voiture: " + e.getMessage());
        }
    }

    private boolean validateInputs() {
        StringBuilder errors = new StringBuilder();

        if (comboMarque.getValue() == null) errors.append("Veuillez sélectionner une marque.\n");
        if (comboModele.getValue() == null) errors.append("Veuillez sélectionner un modèle.\n");
        if (txtAnnee.getText().isEmpty()) errors.append("Veuillez entrer une année.\n");
        if (txtPrix.getText().isEmpty()) errors.append("Veuillez entrer un prix.\n");
        if (txtPuissanceFiscale.getText().isEmpty()) errors.append("Veuillez entrer la puissance fiscale.\n");
        if (txtKilometrage.getText().isEmpty()) errors.append("Veuillez entrer le kilométrage.\n");
        if (comboBoiteVitesse.getValue() == null) errors.append("Veuillez sélectionner une boîte de vitesse.\n");
        if (comboCarburant.getValue() == null) errors.append("Veuillez sélectionner un type de carburant.\n");
        if (txtCylindree.getText().isEmpty()) errors.append("Veuillez entrer la cylindrée.\n");
        if (txtNombrePortes.getText().isEmpty()) errors.append("Veuillez entrer le nombre de portes.\n");

        if (errors.length() > 0) {
            showAlert(Alert.AlertType.ERROR, "Erreur de validation", errors.toString());
            return false;
        }
        return true;
    }

    private String saveImage(File file) throws IOException {
        String targetDir = "src/main/resources/images/voitures/";
        Path targetPath = Paths.get(targetDir);
        if (!Files.exists(targetPath)) {
            Files.createDirectories(targetPath);
        }

        String fileName = System.currentTimeMillis() + "_" + file.getName();
        Path destination = targetPath.resolve(fileName);
        Files.copy(file.toPath(), destination, StandardCopyOption.REPLACE_EXISTING);
        return "images/voitures/" + fileName;
    }

    private void showAlert(Alert.AlertType type, String title, String content) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setContentText(content);
        alert.showAndWait();
    }

    private void clearForm() {
        comboMarque.setValue(null);
        comboModele.setValue(null);
        txtAnnee.clear();
        txtPrix.clear();
        txtPuissanceFiscale.clear();
        txtKilometrage.clear();
        comboBoiteVitesse.setValue(null);
        comboCarburant.setValue(null);
        txtCylindree.clear();
        txtNombrePortes.clear();
        imageViewVoiture.setImage(null);
        selectedImageFile = null;
    }

    @FXML
    private void handleBack() {
        Stage stage = (Stage) btnChooseImage.getScene().getWindow();
        stage.close();
    }
}
