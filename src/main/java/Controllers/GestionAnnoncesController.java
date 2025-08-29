package Controllers;

import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Optional;
import java.util.logging.Level;
import java.util.logging.Logger;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.scene.control.Dialog;
import javafx.scene.control.DialogPane;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import models.Annonce;
import utils.MyDb;

public class GestionAnnoncesController {
    private static final Logger LOGGER = Logger.getLogger(GestionAnnoncesController.class.getName());

    @FXML
    private ListView<String> listViewAnnonces;

    @FXML
    private TextField searchField;

    @FXML
    private VBox welcomePane;

    @FXML
    private VBox annonceDetailsPane;
    @FXML
    private Label detailsTitreLabel;
    @FXML
    private javafx.scene.control.Label detailsDescriptionText;
    @FXML
    private Label detailsMarqueLabel, detailsModeleLabel, detailsAnneeLabel, detailsPrixLabel;
    @FXML
    private ImageView detailsVoitureImageView;

    @FXML
    private VBox invalidAnnoncesPane;
    @FXML
    private ListView<String> listViewInvalidAnnonces;
    @FXML
    private VBox invalidDetailsPane;
    @FXML
    private Label invalidDetailsTitreLabel;
    @FXML
    private javafx.scene.control.Label invalidDetailsDescriptionText;
    @FXML
    private Label invalidDetailsMarqueLabel, invalidDetailsModeleLabel, invalidDetailsAnneeLabel, invalidDetailsPrixLabel;
    @FXML
    private ImageView invalidDetailsVoitureImageView;



    private final ObservableList<String> annonces = FXCollections.observableArrayList();
    private final ObservableList<String> invalidAnnonces = FXCollections.observableArrayList();
    private int selectedAnnonceId = -1;
    private int selectedInvalidAnnonceId = -1;
    private String originalTitre = "";
    private String originalDescription = "";

    @FXML
    public void initialize() {
        ajouterColonneValidation();
        chargerAnnonces();
        listViewAnnonces.setItems(annonces);
        listViewAnnonces.getSelectionModel().selectedItemProperty().addListener((obs, oldSelection, newSelection) -> {
            if (newSelection != null) {
                afficherDetailsAnnonce(newSelection);
            }
        });

        listViewInvalidAnnonces.setItems(invalidAnnonces);
        listViewInvalidAnnonces.getSelectionModel().selectedItemProperty().addListener((obs, oldSelection, newSelection) -> {
            if (newSelection != null) {
                afficherDetailsAnnonceInvalide(newSelection);
            }
        });

        showWelcomePane();

        annonceDetailsPane.widthProperty().addListener((obs, oldW, newW) -> {
            detailsDescriptionText.setMaxWidth(newW.doubleValue() - 60); // account for padding
        });

        invalidDetailsPane.widthProperty().addListener((obs, oldW, newW) -> {
            invalidDetailsDescriptionText.setMaxWidth(newW.doubleValue() - 60);
        });

            if (searchField != null) {
                searchField.textProperty().addListener((observable, oldValue, newValue) -> {
                    String searchText = newValue.toLowerCase();
                    listViewAnnonces.setItems(annonces.filtered(annonce -> annonce.toLowerCase().contains(searchText)));
                });
            }
    }

    private void ajouterColonneValidation() {
        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) return;

        try {
            String checkSql = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'annonce' AND COLUMN_NAME = 'valider'";
            try (PreparedStatement checkStmt = conn.prepareStatement(checkSql);
                 ResultSet rs = checkStmt.executeQuery()) {

                if (rs.next() && rs.getInt(1) == 0) {
                    String alterSql = "ALTER TABLE annonce ADD COLUMN valider INT DEFAULT 0";
                    try (PreparedStatement alterStmt = conn.prepareStatement(alterSql)) {
                        alterStmt.executeUpdate();
                        LOGGER.info("Colonne 'valider' ajoutée à la table annonce");
                    }
                }
            }
        } catch (SQLException e) {
            LOGGER.log(Level.WARNING, "Impossible de vérifier/ajouter la colonne valider", e);
        }
    }

    private void showWelcomePane() {
        welcomePane.setVisible(true);
        annonceDetailsPane.setVisible(false);
        invalidAnnoncesPane.setVisible(false);
    }

    private void showDetailsPane() {
        welcomePane.setVisible(false);
        annonceDetailsPane.setVisible(true);
        invalidAnnoncesPane.setVisible(false);
    }

    private void chargerAnnonces() {
        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) return;

        String sql = "SELECT a.id, a.titre, a.description, v.marque, v.modele FROM annonce a JOIN voiture v ON a.voiture_id = v.id WHERE a.valider = 1 ORDER BY a.id DESC";
        try (PreparedStatement pstmt = conn.prepareStatement(sql);
             ResultSet rs = pstmt.executeQuery()) {

            annonces.clear();
            while (rs.next()) {
                int id = rs.getInt("id");
                String titre = rs.getString("titre");
                String marque = rs.getString("marque");
                String modele = rs.getString("modele");
                annonces.add(id + " - " + titre + " (" + marque + " " + modele + ")");
            }
        } catch (SQLException e) {
            LOGGER.log(Level.SEVERE, "Erreur lors du chargement des annonces", e);
            showAlert(Alert.AlertType.ERROR, "Erreur lors du chargement des annonces: " + e.getMessage());
        }
    }

    private void chargerAnnoncesInvalides() {
        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) return;

        String sql = "SELECT a.id, a.titre, a.description, v.marque, v.modele FROM annonce a JOIN voiture v ON a.voiture_id = v.id WHERE a.valider = 0 ORDER BY a.id DESC";
        try (PreparedStatement pstmt = conn.prepareStatement(sql);
             ResultSet rs = pstmt.executeQuery()) {

            invalidAnnonces.clear();
            while (rs.next()) {
                int id = rs.getInt("id");
                String titre = rs.getString("titre");
                String marque = rs.getString("marque");
                String modele = rs.getString("modele");
                invalidAnnonces.add(id + " - " + titre + " (" + marque + " " + modele + ") - EN ATTENTE");
            }
        } catch (SQLException e) {
            LOGGER.log(Level.SEVERE, "Erreur lors du chargement des annonces invalides", e);
            showAlert(Alert.AlertType.ERROR, "Erreur lors du chargement des annonces invalides: " + e.getMessage());
        }
    }

    private void afficherDetailsAnnonce(String annonce) {
        String[] parts = annonce.split(" - ", 2);
        selectedAnnonceId = Integer.parseInt(parts[0]);

        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) return;

        String sql = "SELECT a.titre, a.description, v.marque, v.modele, v.annee, v.prix, v.image FROM annonce a JOIN voiture v ON a.voiture_id = v.id WHERE a.id = ?";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, selectedAnnonceId);
            try (ResultSet rs = pstmt.executeQuery()) {
                if (rs.next()) {
                    originalTitre = rs.getString("titre");
                    originalDescription = rs.getString("description");

                    detailsTitreLabel.setText(rs.getString("titre"));
                    detailsDescriptionText.setText(rs.getString("description"));
                    detailsMarqueLabel.setText("Marque: " + rs.getString("marque"));
                    detailsModeleLabel.setText("Modèle: " + rs.getString("modele"));
                    detailsAnneeLabel.setText("Année: " + rs.getInt("annee"));
                    detailsPrixLabel.setText("Prix: " + rs.getDouble("prix") + " €");

                    String imagePath = rs.getString("image");
                    if (imagePath != null && !imagePath.isEmpty()) {
                        try {
                            detailsVoitureImageView.setImage(new Image("file:" + imagePath, true));
                        } catch (Exception e) {
                            detailsVoitureImageView.setImage(null);
                        }
                    } else {
                        detailsVoitureImageView.setImage(null);
                    }

                    showDetailsPane();
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur lors du chargement des détails: " + e.getMessage());
        }
    }

    private void afficherDetailsAnnonceInvalide(String annonce) {
        String[] parts = annonce.split(" - ", 2);
        selectedInvalidAnnonceId = Integer.parseInt(parts[0]);

        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) return;

        String sql = "SELECT a.titre, a.description, v.marque, v.modele, v.annee, v.prix, v.image FROM annonce a JOIN voiture v ON a.voiture_id = v.id WHERE a.id = ?";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, selectedInvalidAnnonceId);
            try (ResultSet rs = pstmt.executeQuery()) {
                if (rs.next()) {
                    invalidDetailsTitreLabel.setText(rs.getString("titre"));
                    invalidDetailsDescriptionText.setText(rs.getString("description"));
                    invalidDetailsMarqueLabel.setText("Marque: " + rs.getString("marque"));
                    invalidDetailsModeleLabel.setText("Modèle: " + rs.getString("modele"));
                    invalidDetailsAnneeLabel.setText("Année: " + rs.getInt("annee"));
                    invalidDetailsPrixLabel.setText("Prix: " + rs.getDouble("prix") + " €");

                    String imagePath = rs.getString("image");
                    if (imagePath != null && !imagePath.isEmpty()) {
                        try {
                            invalidDetailsVoitureImageView.setImage(new Image("file:" + imagePath, true));
                        } catch (Exception e) {
                            invalidDetailsVoitureImageView.setImage(null);
                        }
                    } else {
                        invalidDetailsVoitureImageView.setImage(null);
                    }

                    invalidDetailsPane.setVisible(true);
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
            showAlert(Alert.AlertType.ERROR, "Erreur lors du chargement des détails: " + e.getMessage());
        }
    }

    @FXML
    private void showInvalidAnnonces() {
        chargerAnnoncesInvalides();

        javafx.scene.control.ListView<String> popupList = new javafx.scene.control.ListView<>();
        popupList.setItems(invalidAnnonces);
        popupList.setStyle(
            "-fx-background-color: #f8f9fa;" +
            "-fx-border-color: #dee2e6;" +
            "-fx-border-radius: 4px;" +
            "-fx-background-radius: 4px;"
        );
        popupList.setCellFactory(lv -> new javafx.scene.control.ListCell<String>() {
            @Override
            protected void updateItem(String item, boolean empty) {
                super.updateItem(item, empty);
                if (empty || item == null) {
                    setText(null);
                    setGraphic(null);
                    setStyle(null);
                } else {
                    setText(item);
                    setStyle(
                        "-fx-padding: 10px;" +
                        "-fx-background-color: transparent;" +
                        "-fx-border-color: transparent transparent #dee2e6 transparent;"
                    );
                    // Add hover effect
                    setOnMouseEntered(e -> setStyle(
                        "-fx-padding: 10px;" +
                        "-fx-background-color: #e9ecef;" +
                        "-fx-border-color: transparent transparent #dee2e6 transparent;"
                    ));
                    setOnMouseExited(e -> setStyle(
                        "-fx-padding: 10px;" +
                        "-fx-background-color: transparent;" +
                        "-fx-border-color: transparent transparent #dee2e6 transparent;"
                    ));
                }
            }
        });

        Label popupTitle = new Label("Annonces Non Validées");
        popupTitle.setStyle(
            "-fx-font-size: 24px;" +
            "-fx-font-weight: bold;" +
            "-fx-text-fill: #212529;" +
            "-fx-padding: 0 0 10 0;"
        );

        VBox popupDetails = new VBox(15);
        popupDetails.setStyle(
            "-fx-background-color: white;" +
            "-fx-padding: 20px;" +
            "-fx-border-radius: 8px;" +
            "-fx-background-radius: 8px;" +
            "-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.1), 10, 0, 0, 3);"
        );

        Label pdTitre = new Label();
        pdTitre.setStyle(
            "-fx-font-size: 18px;" +
            "-fx-font-weight: bold;" +
            "-fx-text-fill: #212529;"
        );

        Label pdDesc = new Label();
        pdDesc.setWrapText(true);
        pdDesc.setStyle(
            "-fx-font-size: 14px;" +
            "-fx-text-fill: #495057;"
        );

        ImageView pdImage = new ImageView();
        pdImage.setFitWidth(300);
        pdImage.setFitHeight(200);
        pdImage.setStyle(
            "-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.1), 5, 0, 0, 1);" +
            "-fx-background-radius: 4px;"
        );

        Label statusLabel = new Label("En attente de validation");
        statusLabel.setStyle(
            "-fx-font-size: 12px;" +
            "-fx-text-fill: #ffc107;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 5px 10px;" +
            "-fx-background-color: rgba(255,193,7,0.1);" +
            "-fx-background-radius: 4px;"
        );

        Button validateBtn = new Button("Valider l'Annonce");
        validateBtn.setGraphic(new javafx.scene.control.Label("✓"));
        validateBtn.setStyle(
            "-fx-background-color: #28a745;" +
            "-fx-text-fill: white;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 10px 20px;" +
            "-fx-background-radius: 4px;" +
            "-fx-cursor: hand;"
        );
        validateBtn.setOnMouseEntered(e -> validateBtn.setStyle(
            "-fx-background-color: #218838;" +
            "-fx-text-fill: white;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 10px 20px;" +
            "-fx-background-radius: 4px;" +
            "-fx-cursor: hand;" +
            "-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.2), 5, 0, 0, 1);"
        ));
        validateBtn.setOnMouseExited(e -> validateBtn.setStyle(
            "-fx-background-color: #28a745;" +
            "-fx-text-fill: white;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 10px 20px;" +
            "-fx-background-radius: 4px;" +
            "-fx-cursor: hand;"
        ));
        validateBtn.setDisable(true);
        
        Button deleteBtn = new Button("Supprimer");
        deleteBtn.setGraphic(new javafx.scene.control.Label("×"));
        deleteBtn.setStyle(
            "-fx-background-color: #dc3545;" +
            "-fx-text-fill: white;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 10px 20px;" +
            "-fx-background-radius: 4px;" +
            "-fx-cursor: hand;"
        );
        deleteBtn.setOnMouseEntered(e -> deleteBtn.setStyle(
            "-fx-background-color: #c82333;" +
            "-fx-text-fill: white;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 10px 20px;" +
            "-fx-background-radius: 4px;" +
            "-fx-cursor: hand;" +
            "-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.2), 5, 0, 0, 1);"
        ));
        deleteBtn.setOnMouseExited(e -> deleteBtn.setStyle(
            "-fx-background-color: #dc3545;" +
            "-fx-text-fill: white;" +
            "-fx-font-weight: bold;" +
            "-fx-padding: 10px 20px;" +
            "-fx-background-radius: 4px;" +
            "-fx-cursor: hand;"
        ));
        deleteBtn.setDisable(true);

        HBox buttonBox = new HBox(15);
        buttonBox.setAlignment(javafx.geometry.Pos.CENTER);
        buttonBox.setStyle("-fx-padding: 20px 0 0 0;");
        buttonBox.getChildren().addAll(validateBtn, deleteBtn);
        
        VBox headerSection = new VBox(5);
        headerSection.getChildren().addAll(statusLabel, pdTitre);
        
        VBox contentSection = new VBox(15);
        contentSection.getChildren().addAll(pdDesc, pdImage);
        
        popupDetails.getChildren().addAll(headerSection, contentSection, buttonBox);

        popupList.getSelectionModel().selectedItemProperty().addListener((obs, oldV, newV) -> {
            if (newV != null) {
                String[] parts = newV.split(" - ", 2);
                int id = Integer.parseInt(parts[0]);
                Connection conn = MyDb.getInstance().getConnection();
                if (conn == null) return;
                String sql = "SELECT a.titre, a.description, v.marque, v.modele, v.annee, v.prix, v.image FROM annonce a JOIN voiture v ON a.voiture_id = v.id WHERE a.id = ?";
                try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
                    pstmt.setInt(1, id);
                    try (ResultSet rs = pstmt.executeQuery()) {
                        if (rs.next()) {
                            pdTitre.setText(rs.getString("titre"));
                            pdDesc.setText(rs.getString("description"));
                            String imagePath = rs.getString("image");
                            if (imagePath != null && !imagePath.isEmpty()) {
                                try { pdImage.setImage(new Image("file:" + imagePath, true)); } catch (Exception e) { pdImage.setImage(null); }
                            } else {
                                pdImage.setImage(null);
                            }
                            selectedInvalidAnnonceId = id;
                            validateBtn.setDisable(false);
                            deleteBtn.setDisable(false);
                        }
                    }
                } catch (SQLException e) {
                    e.printStackTrace();
                }
            }
        });

        validateBtn.setOnAction(e -> {
            validateAnnonce();
            chargerAnnoncesInvalides();
            popupList.getSelectionModel().clearSelection();
            pdTitre.setText("");
            pdDesc.setText("");
            pdImage.setImage(null);
            validateBtn.setDisable(true);
            deleteBtn.setDisable(true);
        });

        deleteBtn.setOnAction(e -> {
            deleteInvalidAnnonce();
            chargerAnnoncesInvalides();
            popupList.getSelectionModel().clearSelection();
            pdTitre.setText("");
            pdDesc.setText("");
            pdImage.setImage(null);
            validateBtn.setDisable(true);
            deleteBtn.setDisable(true);
        });

        HBox headerBox = new HBox(10);
        headerBox.setAlignment(javafx.geometry.Pos.CENTER_LEFT);
        headerBox.setStyle(
            "-fx-padding: 20px;" +
            "-fx-background-color: white;" +
            "-fx-border-color: transparent transparent #dee2e6 transparent;" +
            "-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.05), 5, 0, 0, 1);"
        );
        
        Label iconLabel = new Label("🔍");
        iconLabel.setStyle("-fx-font-size: 24px;");
        headerBox.getChildren().addAll(iconLabel, popupTitle);

        HBox content = new HBox(0);
        
        VBox leftPanel = new VBox(0);
        leftPanel.setPrefWidth(350);
        leftPanel.setStyle(
            "-fx-background-color: #f8f9fa;" +
            "-fx-border-color: transparent #dee2e6 transparent transparent;"
        );
        leftPanel.getChildren().add(popupList);

        VBox rightPanel = new VBox(0);
        rightPanel.setPrefWidth(550);
        rightPanel.setStyle(
            "-fx-background-color: white;" +
            "-fx-padding: 20px;"
        );
        rightPanel.getChildren().add(popupDetails);

        content.getChildren().addAll(leftPanel, rightPanel);

        VBox root = new VBox(0);
        root.getChildren().addAll(headerBox, content);
        root.setStyle("-fx-background-color: white;");

        javafx.stage.Stage stage = new javafx.stage.Stage();
        stage.setTitle("Gestion des Annonces");
        stage.setMinWidth(900);
        stage.setMinHeight(600);
        
        javafx.scene.Scene scene = new javafx.scene.Scene(root, 900, 600);
        scene.setFill(javafx.scene.paint.Color.WHITE);
        
        stage.setScene(scene);
        stage.initModality(javafx.stage.Modality.APPLICATION_MODAL);
        
        stage.centerOnScreen();
        
        stage.initStyle(javafx.stage.StageStyle.DECORATED);
        
        stage.showAndWait();
    }

    @FXML
    private void showAllAnnonces() {
        chargerAnnonces();
        showWelcomePane();
    }

    @FXML
    private void refreshInvalidAnnonces() {
        chargerAnnoncesInvalides();
        invalidDetailsPane.setVisible(false);
        selectedInvalidAnnonceId = -1;
    }

    @FXML
    private void validateAnnonce() {
        if (selectedInvalidAnnonceId == -1) return;

        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) return;

        String sql = "UPDATE annonce SET valider = 1 WHERE id = ?";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, selectedInvalidAnnonceId);

            int rowsAffected = pstmt.executeUpdate();
            if (rowsAffected > 0) {
                showAlert(Alert.AlertType.INFORMATION, "Annonce validée avec succès!");
                refreshInvalidAnnonces();
                chargerAnnonces(); // Refresh main list too
            } else {
                showAlert(Alert.AlertType.ERROR, "Erreur lors de la validation.");
            }
        } catch (SQLException e) {
            LOGGER.log(Level.SEVERE, "Erreur lors de la validation de l'annonce", e);
            showAlert(Alert.AlertType.ERROR, "Erreur lors de la validation: " + e.getMessage());
        }
    }

    @FXML
    private void deleteInvalidAnnonce() {
        if (selectedInvalidAnnonceId == -1) return;

        Alert confirmation = new Alert(Alert.AlertType.CONFIRMATION);
        confirmation.setTitle("Confirmation de suppression");
        confirmation.setHeaderText("Êtes-vous sûr de vouloir supprimer définitivement cette annonce ?");
        confirmation.setContentText("Cette action ne peut pas être annulée.");

        if (confirmation.showAndWait().get() == ButtonType.OK) {
            Connection conn = MyDb.getInstance().getConnection();
            if (conn == null) return;

            String sql = "DELETE FROM annonce WHERE id = ?";
            try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
                pstmt.setInt(1, selectedInvalidAnnonceId);
                int rowsAffected = pstmt.executeUpdate();

                if (rowsAffected > 0) {
                    showAlert(Alert.AlertType.INFORMATION, "Annonce supprimée définitivement!");
                    refreshInvalidAnnonces();
                } else {
                    showAlert(Alert.AlertType.ERROR, "Erreur lors de la suppression.");
                }
            } catch (SQLException e) {
                e.printStackTrace();
                showAlert(Alert.AlertType.ERROR, "Erreur lors de la suppression: " + e.getMessage());
            }
        }
    }

    @FXML
    private void modifierAnnonce() {
        if (selectedAnnonceId == -1) return;

        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/edit-annonce-dialog.fxml"));
            DialogPane dialogPane = loader.load();

            Dialog<ButtonType> dialog = new Dialog<>();
            dialog.setDialogPane(dialogPane);
            dialog.setTitle("Modifier l'annonce");

            ModifierAnnonceController controller = loader.getController();
            Annonce annonce = new Annonce();
            annonce.setId(selectedAnnonceId);
            annonce.setTitre(originalTitre);
            annonce.setDescription(originalDescription);
            controller.initData(annonce);

            dialog.getDialogPane().lookupButton(ButtonType.OK).getStyleClass().add("modern-button");
            dialog.getDialogPane().lookupButton(ButtonType.CANCEL).getStyleClass().add("modern-button-secondary");

            Optional<ButtonType> clickedButton = dialog.showAndWait();
            
            if (clickedButton.isPresent() && clickedButton.get() == ButtonType.OK) {
                Annonce updatedAnnonce = controller.getUpdatedAnnonce();
                
                Connection conn = MyDb.getInstance().getConnection();
                if (conn == null) return;

                String sql = "UPDATE annonce SET titre = ?, description = ? WHERE id = ?";
                try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
                    pstmt.setString(1, updatedAnnonce.getTitre());
                    pstmt.setString(2, updatedAnnonce.getDescription());
                    pstmt.setInt(3, updatedAnnonce.getIdAnnonce());

                    int rowsAffected = pstmt.executeUpdate();
                    if (rowsAffected > 0) {
                        showAlert(Alert.AlertType.INFORMATION, "Annonce mise à jour avec succès!");
                        chargerAnnonces(); 

                        detailsTitreLabel.setText(updatedAnnonce.getTitre());
                        detailsDescriptionText.setText(updatedAnnonce.getDescription());
                        originalTitre = updatedAnnonce.getTitre();
                        originalDescription = updatedAnnonce.getDescription();
                    } else {
                        showAlert(Alert.AlertType.ERROR, "Erreur lors de la mise à jour.");
                    }
                }
            }
        } catch (IOException | SQLException e) {
            LOGGER.log(Level.SEVERE, "Error updating annonce", e);
            showAlert(Alert.AlertType.ERROR, "Erreur lors de la modification: " + e.getMessage());
        }
    }

    @FXML
    private void supprimerAnnonce() {
        if (selectedAnnonceId == -1) return;

        Alert confirmation = new Alert(Alert.AlertType.CONFIRMATION);
        confirmation.setTitle("Confirmation de suppression");
        confirmation.setHeaderText("Êtes-vous sûr de vouloir supprimer cette annonce ?");
        confirmation.setContentText("Cette action ne peut pas être annulée.");

        if (confirmation.showAndWait().get() == ButtonType.OK) {
            Connection conn = MyDb.getInstance().getConnection();
            if (conn == null) return;

            String sql = "UPDATE annonce SET valider = 0 WHERE id = ?";
            try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
                pstmt.setInt(1, selectedAnnonceId);
                int rowsAffected = pstmt.executeUpdate();

                if (rowsAffected > 0) {
                    showAlert(Alert.AlertType.INFORMATION, "Annonce déplacée vers les annonces invalides!");
                    chargerAnnonces(); // Refresh the list
                    selectedAnnonceId = -1;
                    showWelcomePane();
                } else {
                    showAlert(Alert.AlertType.ERROR, "Erreur lors de la suppression.");
                }
            } catch (SQLException e) {
                e.printStackTrace();
                showAlert(Alert.AlertType.ERROR, "Erreur lors de la suppression: " + e.getMessage());
            }
        }
    }

    private void showAlert(Alert.AlertType type, String message) {
        Alert alert = new Alert(type, message, ButtonType.OK);
        if (type == Alert.AlertType.ERROR) {
            LOGGER.log(Level.SEVERE, message);
        }
        alert.showAndWait();
    }
}
