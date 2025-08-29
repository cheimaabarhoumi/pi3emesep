package Controllers;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.util.List;
import java.util.Optional;

import com.google.zxing.BarcodeFormat;
import com.google.zxing.WriterException;
import com.google.zxing.client.j2se.MatrixToImageWriter;
import com.google.zxing.common.BitMatrix;
import com.google.zxing.qrcode.QRCodeWriter;

import javafx.animation.FadeTransition;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.print.PrinterJob;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.ButtonType;
import javafx.scene.control.Label;
import javafx.scene.control.ListCell;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.input.MouseEvent;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import javafx.util.Duration;
import models.Voiture;
import services.ServiceVoiture;
public class VoitureController {

    @FXML
    private ListView<Voiture> listviewVoiture;
    @FXML
    private ImageView imageViewVoiture;
    @FXML
    private Label lblMarque, lblModele, lblAnnee, lblPrix, lblKilometrage;
    @FXML
    private VBox detailsContainer;
    @FXML
    private ImageView qrCodeImageView;
    @FXML
    private TextField searchField;  

    private final ServiceVoiture serviceVoiture = new ServiceVoiture();
    private final ObservableList<Voiture> voituresObservable = FXCollections.observableArrayList();
    private final ObservableList<Voiture> filteredList = FXCollections.observableArrayList();  // For filtered results

    public void initialize() {
        System.out.println("🚀 Initialisation de la liste des voitures...");
        loadVoitures();
        listviewVoiture.setOnMouseClicked(this::handleSelectVoiture);
        
        filteredList.setAll(voituresObservable);
        
        searchField.textProperty().addListener((observable, oldValue, newValue) -> {
            String searchText = newValue.toLowerCase();
            filteredList.setAll(voituresObservable.filtered(voiture -> 
                voiture.getMarque().toLowerCase().contains(searchText)
            ));
            listviewVoiture.setItems(filteredList);
        });
    }

    private void loadVoitures() {
        List<Voiture> voitureList = serviceVoiture.getAll();
        voituresObservable.setAll(voitureList);
        listviewVoiture.setItems(voituresObservable);
        listviewVoiture.setCellFactory(lv -> new ListCell<Voiture>() {
            @Override
            protected void updateItem(Voiture voiture, boolean empty) {
                super.updateItem(voiture, empty);
                if (empty || voiture == null) {
                    setText(null);
                    setGraphic(null);
                } else {
                    setText(voiture.getMarque() + " " + voiture.getModele() + " (" + voiture.getAnnee() + ")");
                }
            }
        });
    }

    @FXML
    private void handleSelectVoiture(MouseEvent event) {
        Voiture selectedVoiture = listviewVoiture.getSelectionModel().getSelectedItem();
        if (selectedVoiture != null) {
            displayVoitureImage(selectedVoiture.getImage());
            displayVoitureDetails(selectedVoiture);
        }
    }

    private void displayVoitureImage(String imagePath) {
        if (imagePath != null && !imagePath.isEmpty()) {
            try {
                Image image = new Image("file:" + imagePath, true);
                imageViewVoiture.setImage(image);
                FadeTransition fadeIn = new FadeTransition(Duration.millis(500), imageViewVoiture);
                fadeIn.setFromValue(0);
                fadeIn.setToValue(1);
                fadeIn.play();
            } catch (Exception e) {
                System.out.println("❌ Erreur lors du chargement de l'image: " + e.getMessage());
                imageViewVoiture.setImage(null);
            }
        } else {
            imageViewVoiture.setImage(null);
        }
    }

    private void displayVoitureDetails(Voiture voiture) {
        lblMarque.setText("Marque: " + voiture.getMarque());
        lblModele.setText("Modèle: " + voiture.getModele());
        lblAnnee.setText("Année: " + voiture.getAnnee());
        lblPrix.setText("Prix: " + voiture.getPrix() + " Tnd");
        lblKilometrage.setText("Kilométrage: " + voiture.getKilometrage() + " km");
        
        generateQRCode(voiture);
        
        detailsContainer.setVisible(true);
    }

    @FXML
    private void handleAdd(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/AddVoiture.fxml"));
            Parent root = loader.load();
            Stage stage = new Stage();
            stage.setTitle("Ajouter une Voiture");
            stage.setScene(new Scene(root));
            stage.show();

        } catch (IOException e) {
            showAlert("Erreur", "Impossible d'ouvrir la fenêtre", "Vérifiez le fichier FXML.");
        }
    }

    @FXML
    private void handleOpenAnnonces(ActionEvent event) {
        try {
            java.net.URL resourceUrl = getClass().getResource("/fxml/annonce.fxml");
            if (resourceUrl == null) {
                throw new IOException("Cannot find resource: /fxml/annonce.fxml");
            }
            
            FXMLLoader loader = new FXMLLoader(resourceUrl);
            Parent root = loader.load();
            
            Stage stage = new Stage();
            stage.setTitle("Gestion des Annonces");
            
            stage.setMinWidth(800);
            stage.setMinHeight(600);
            stage.setMaxWidth(1200);
            stage.setMaxHeight(800);
            
            Scene scene = new Scene(root);
            stage.setScene(scene);
            
            stage.centerOnScreen();
            
            stage.initModality(javafx.stage.Modality.APPLICATION_MODAL);
            stage.show();
            
        } catch (IOException e) {
            System.err.println("❌ Erreur lors de l'ouverture des annonces: " + e.getMessage());
            e.printStackTrace();
            showAlert("Erreur", "Impossible d'ouvrir les annonces", "Une erreur s'est produite lors de l'ouverture du fichier FXML: " + e.getMessage());
        }
    }

    @FXML
    private void handleOpenAnnoncesview(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/annonces-view.fxml"));
            Parent root = loader.load();
            Stage stage = new Stage();
            stage.setTitle("Gestion des Annonces");
            stage.setScene(new Scene(root));
            stage.show();
        } catch (IOException e) {
            showAlert("Erreur", "Impossible d'ouvrir la vue des annonces", "Erreur lors de l'ouverture de la fenêtre.");
        }
    }

    @FXML
    private void handleDeleteVoiture(ActionEvent event) {
        Voiture selectedVoiture = listviewVoiture.getSelectionModel().getSelectedItem();
        if (selectedVoiture != null) {
            Alert confirmation = new Alert(Alert.AlertType.CONFIRMATION);
            confirmation.setTitle("Confirmation");
            confirmation.setHeaderText("Supprimer la voiture");
            confirmation.setContentText("Êtes-vous sûr de vouloir supprimer cette voiture ?");
            Optional<ButtonType> result = confirmation.showAndWait();
            if (result.isPresent() && result.get() == ButtonType.OK) {
                serviceVoiture.delete(selectedVoiture);
                loadVoitures();
                showAlert("Succès", "Suppression réussie", "La voiture a été supprimée avec succès.");
            }
        } else {
            showAlert("Erreur", "Aucune voiture sélectionnée", "Veuillez sélectionner une voiture à supprimer.");
        }
    }

    private void showAlert(String title, String header, String content) {
        Alert alert = new Alert(Alert.AlertType.INFORMATION);
        alert.setTitle(title);
        alert.setHeaderText(header);
        alert.setContentText(content);
        alert.showAndWait();
    }

    @FXML
    private void handleUpdateVoiture() {
        Voiture selectedVoiture = listviewVoiture.getSelectionModel().getSelectedItem();
        if (selectedVoiture != null && selectedVoiture.getIdVoiture() > 0) {  // 🔹 Évite ID = 0
            try {
                FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/UpdateVoiture.fxml"));
                Parent root = loader.load();

                UpdateVoitureController controller = loader.getController();
                controller.setVoiture(selectedVoiture);

                Stage stage = new Stage();
                stage.setTitle("Modifier Voiture");
                stage.setScene(new Scene(root));
                stage.showAndWait();

                loadVoitures(); 
            } catch (IOException e) {
                showAlert("Erreur", "Impossible d'ouvrir la fenêtre de modification", "Erreur lors du chargement de la vue.");
            }
        } else {
            int badId = (selectedVoiture == null) ? -1 : selectedVoiture.getIdVoiture();
            System.out.println("⚠️ Cannot open update: selected voiture is invalid. ID=" + badId + ", object=" + selectedVoiture);
            showAlert("Erreur", "Voiture invalide", "Impossible de modifier cette voiture ! (ID = " + badId + ")\nVeuillez sélectionner une voiture sauvegardée.");
        }
    }
    @FXML
    private void handlePrint() {
        PrinterJob printerJob = PrinterJob.createPrinterJob();
        if (printerJob != null) {
            boolean success = printerJob.printPage(listviewVoiture);
            if (success) {
                printerJob.endJob();
                System.out.println("Printing completed successfully!");
            } else {
                System.out.println("Printing failed.");
            }
        }
    }

    private void generateQRCode(Voiture voiture) {
        if (voiture == null) {
            System.err.println("Erreur: Voiture est null");
            qrCodeImageView.setImage(null);
            return;
        }

        try {
            StringBuilder qrData = new StringBuilder();

            qrData.append("CAR DETAILS\n");
            qrData.append("===========\n");

            qrData.append("ID: ").append(voiture.getIdVoiture()).append("\n");
            qrData.append("Brand: ").append(safeString(voiture.getMarque())).append("\n");
            qrData.append("Model: ").append(safeString(voiture.getModele())).append("\n");
            qrData.append("Year: ").append(voiture.getAnnee()).append("\n");
            qrData.append("Price: ").append(String.format("%.0f", voiture.getPrix())).append(" TND\n");
            qrData.append("Mileage: ").append(voiture.getKilometrage()).append(" km\n");

            if (voiture.getpuissance_fiscale() > 0) {
                qrData.append("Power: ").append(voiture.getpuissance_fiscale()).append(" CV\n");
            }
            if (voiture.getboite_vitesse() != null && !voiture.getboite_vitesse().trim().isEmpty()) {
                qrData.append("Transmission: ").append(voiture.getboite_vitesse()).append("\n");
            }
            if (voiture.getCarburant() != null && !voiture.getCarburant().trim().isEmpty()) {
                qrData.append("Fuel: ").append(voiture.getCarburant()).append("\n");
            }
            if (voiture.getCylindree() != null && !voiture.getCylindree().trim().isEmpty()) {
                qrData.append("Engine: ").append(voiture.getCylindree()).append("\n");
            }
            if (voiture.getnombre_portes() > 0) {
                qrData.append("Doors: ").append(voiture.getnombre_portes()).append("\n");
            }

            qrData.append("===========\n");
            qrData.append("MechaRift App");

            String qrText = qrData.toString();

            if (qrText.length() < 20) {
                qrText = "CAR ID: " + voiture.getIdVoiture() + " - " + safeString(voiture.getMarque()) + " " + safeString(voiture.getModele());
            }

            if (qrText.length() > 500) {
                qrText = qrText.substring(0, 497) + "...";
            }

            System.out.println("DEBUG: QR Text length: " + qrText.length());
            System.out.println("DEBUG: QR Text preview:");
            System.out.println(qrText);
            System.out.println("DEBUG: QR Text contains newlines: " + qrText.contains("\n"));

            QRCodeWriter qrCodeWriter = new QRCodeWriter();
            BitMatrix bitMatrix = qrCodeWriter.encode(qrText, BarcodeFormat.QR_CODE, 300, 300);

            ByteArrayOutputStream outputStream = new ByteArrayOutputStream();
            MatrixToImageWriter.writeToStream(bitMatrix, "PNG", outputStream);
            ByteArrayInputStream inputStream = new ByteArrayInputStream(outputStream.toByteArray());
            Image qrImage = new Image(inputStream);

            qrCodeImageView.setImage(qrImage);

            System.out.println("✅ QR Code generated successfully!");
            System.out.println("Data length: " + qrText.length() + " characters");

        } catch (WriterException e) {
            System.err.println("❌ WriterException: " + e.getMessage());
            e.printStackTrace();
            qrCodeImageView.setImage(null);
        } catch (java.io.IOException e) {
            System.err.println("❌ IOException: " + e.getMessage());
            e.printStackTrace();
            qrCodeImageView.setImage(null);
        } catch (Exception e) {
            System.err.println("❌ Unexpected error: " + e.getMessage());
            e.printStackTrace();
            qrCodeImageView.setImage(null);
        }
    }

    private String safeString(String value) {
        return (value != null && !value.trim().isEmpty()) ? value : "N/A";
    }
}
