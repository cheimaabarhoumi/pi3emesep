package Controllers;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.Properties;

import jakarta.mail.Message;
import jakarta.mail.MessagingException;
import jakarta.mail.PasswordAuthentication;
import jakarta.mail.Session;
import jakarta.mail.Transport;
import jakarta.mail.internet.InternetAddress;
import jakarta.mail.internet.MimeMessage;
import javafx.animation.FadeTransition;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.scene.control.ComboBox;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.VBox;
import javafx.scene.text.Text;
import javafx.util.Duration;
import utils.MyDb;
public class AnnonceController {

    @FXML private TextField titreField;
    @FXML private TextArea descriptionField;
    @FXML private ListView<String> listViewAnnonces;
    @FXML private ComboBox<String> voitureComboBox;
    @FXML private Label marqueLabel, modeleLabel, anneeLabel, prixLabel;
    @FXML private ImageView voitureImageView;
    
    @FXML private VBox formulairePane, detailsPane;
    @FXML private Label formTitleLabel, detailsTitreLabel;
    @FXML private Text detailsDescriptionText;
    @FXML private Label detailsMarqueLabel, detailsModeleLabel, detailsAnneeLabel, detailsPrixLabel;
    @FXML private ImageView detailsVoitureImageView;
    @FXML private Button annulerButton, enregistrerButton;

    private final ObservableList<String> annonces = FXCollections.observableArrayList();
    private final Map<String, Integer> voitureInfos = new HashMap<>();
    private Integer annonceIdEnEdition = null; // Pour suivre l'annonce en cours d'édition

    @FXML
    public void initialize() {
        try {
            if (formulairePane != null) {
                formulairePane.setVisible(false);
            }
            if (detailsPane != null) {
                detailsPane.setVisible(false);
            }

            chargerVoitures();
            chargerAnnonces();
            
            if (voitureComboBox != null) {
                voitureComboBox.setOnAction(event -> handleSelectionVoiture());
            }
            
            if (listViewAnnonces != null) {
                listViewAnnonces.setOnMouseClicked(event -> {
                    if (event.getClickCount() == 1) {
                        afficherDetailsAnnonce();
                    }
                });
            }
        } catch (Exception e) {
            System.err.println("❌ Erreur lors de l'initialisation: " + e.getMessage());
            e.printStackTrace();
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors de l'ouverture de la fenêtre.");
        }
    }
    
    private void chargerAnnonces() {
        annonces.clear();
        Connection conn = MyDb.getInstance().getConnection();
        String sql = "SELECT a.id, a.titre, a.description, v.marque, v.modele " +
                    "FROM annonce a JOIN voiture v ON a.voiture_id = v.id " +
                    "WHERE a.valider = 1 " +
                    "ORDER BY a.id DESC";
                    
        try (PreparedStatement pstmt = conn.prepareStatement(sql);
             ResultSet rs = pstmt.executeQuery()) {
            
            while (rs.next()) {
                String titre = rs.getString("titre");
                String marque = rs.getString("marque");
                String modele = rs.getString("modele");
                annonces.add(String.format("%s (%s %s)", titre, marque, modele));
            }
            
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors du chargement des annonces: " + e.getMessage());
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors du chargement des annonces.");
        }
        
        listViewAnnonces.setItems(annonces);
    }

    private void chargerVoitures() {
        ObservableList<String> voitures = FXCollections.observableArrayList();
        Connection conn = MyDb.getInstance().getConnection();

        if (conn == null) {
            System.err.println("❌ Erreur: La connexion à la base de données est null");
            afficherAlerte(Alert.AlertType.ERROR, "Erreur de connexion à la base de données.");
            return;
        }

        String sql = "SELECT id, marque, modele FROM voiture";
        try {
            System.out.println("🔍 Exécution de la requête: " + sql);
            
            PreparedStatement pstmt = conn.prepareStatement(sql);
            ResultSet rs = pstmt.executeQuery();

            System.out.println("📚 Début du chargement des voitures...");
            voitureInfos.clear(); 
            int count = 0;

            while (rs.next()) {
                int id = rs.getInt("id");
                String marque = rs.getString("marque");
                String modele = rs.getString("modele");
                String displayText = marque + " " + modele;
                
                System.out.println("✅ Voiture trouvée - ID: " + id + ", Marque: " + marque + ", Modèle: " + modele);
                count++;

                System.out.println("ID: " + id + ", Voiture: " + displayText);

                voitures.add(displayText);
                voitureInfos.put(displayText, id); // Stocke l'ID avec le texte d'affichage complet
            }

            voitureComboBox.setItems(voitures); // Set items directly, no need for a new observable list

            System.out.println("✅ Total voitures chargées: " + voitures.size());

        } catch (SQLException e) {
            System.err.println("❌ Erreur SQL lors du chargement des voitures: " + e.getMessage());
            e.printStackTrace();
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors du chargement des voitures: " + e.getMessage());
        }
    }

    @FXML
    private void handleSelectionVoiture() {
        String modeleSelectionne = voitureComboBox.getValue();
        if (modeleSelectionne == null || !voitureInfos.containsKey(modeleSelectionne)) {
            System.out.println("⚠ Aucune voiture sélectionnée ou inconnue.");
            return;
        }

        int idVoiture = voitureInfos.get(modeleSelectionne);

        Connection conn = MyDb.getInstance().getConnection();
        String sql = "SELECT marque, modele, annee, prix, image FROM voiture WHERE id = ?";

        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, idVoiture);
            try (ResultSet rs = pstmt.executeQuery()) {
                if (rs.next()) {
                    marqueLabel.setText("Marque : " + rs.getString("marque"));
                    modeleLabel.setText("Modèle : " + rs.getString("modele"));
                    anneeLabel.setText("Année : " + rs.getInt("annee"));
                    prixLabel.setText("Prix : " + rs.getDouble("prix") + " €");

                    String imagePath = rs.getString("image");
                    displayVoitureImage(imagePath); // ✅ Appel de la fonction de chargement
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors de la récupération des informations de la voiture.");
        }
    }

    private void displayVoitureImage(String imagePath) {
        if (imagePath != null && !imagePath.isEmpty()) {
            try {
                Image image;

                if (imagePath.startsWith("/") || imagePath.startsWith("src/main/resources")) {
                    String resourcePath = imagePath.replace("src/main/resources", "");
                    System.out.println("🔹 Chargement de l'image depuis les ressources : " + resourcePath);

                    if (getClass().getResource(resourcePath) != null) {
                        image = new Image(getClass().getResource(resourcePath).toExternalForm());
                    } else {
                        throw new Exception("Ressource introuvable.");
                    }
                } else {
                    System.out.println("🔹 Chargement de l'image en tant que fichier : file:" + imagePath);
                    image = new Image("file:" + imagePath, true);
                }

                voitureImageView.setImage(image);

                // Effet de fondu (transition d'apparition)
                FadeTransition fadeIn = new FadeTransition(Duration.millis(500), voitureImageView);
                fadeIn.setFromValue(0);
                fadeIn.setToValue(1);
                fadeIn.play();

            } catch (Exception e) {
                System.out.println("❌ Erreur lors du chargement de l'image: " + e.getMessage());
                voitureImageView.setImage(null);
            }
        } else {
            voitureImageView.setImage(null);
        }
    }


    @FXML
    private void ajouterAnnonce() {
        String titre = titreField.getText();
        String description = descriptionField.getText();
        String voitureSelectionnee = voitureComboBox.getValue();

        if (titre.isEmpty() || description.isEmpty() || voitureSelectionnee == null) {
            afficherAlerte(Alert.AlertType.WARNING, "Veuillez remplir tous les champs.");
            return;
        }

        if (!voitureInfos.containsKey(voitureSelectionnee)) {
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors de la récupération de la voiture.");
            return;
        }

        int idVoiture = voitureInfos.get(voitureSelectionnee); // ❌ Suppression de .getKey()

        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) {
            afficherAlerte(Alert.AlertType.ERROR, "Erreur de connexion à la base de données.");
            return;
        }

        String sql = "INSERT INTO annonce (titre, description, voiture_id, valider) VALUES (?, ?, ?, 0)";
        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, titre);
            pstmt.setString(2, description);
            pstmt.setInt(3, idVoiture); // Using idVoiture column name

            pstmt.executeUpdate();

            afficherAlerte(Alert.AlertType.INFORMATION, "Annonce ajoutée avec succès !");
            annonces.add(titre + " - " + description + " (" + voitureSelectionnee + ")");
            
            // Envoyer un email aux administrateurs
            sendEmailToAdmins(titre, description, voitureSelectionnee);
            
            titreField.clear();
            descriptionField.clear();
            voitureComboBox.getSelectionModel().clearSelection();
        } catch (SQLException e) {
            e.printStackTrace();
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors de l'ajout de l'annonce.");
        }
    }




    @FXML
    private void afficherFormulaireAjout() {
        annonceIdEnEdition = null;
        titreField.clear();
        descriptionField.clear();
        voitureComboBox.getSelectionModel().clearSelection();
        voitureImageView.setImage(null);
        marqueLabel.setText("Marque : -");
        modeleLabel.setText("Modèle : -");
        anneeLabel.setText("Année : -");
        prixLabel.setText("Prix : -");
        
        // Show form
        formTitleLabel.setText("Nouvelle Annonce");
        detailsPane.setVisible(false);
        formulairePane.setVisible(true);
    }

    @FXML
    private void annulerFormulaire() {
        formulairePane.setVisible(false);
        if (detailsPane.isVisible()) {
            detailsPane.setVisible(true);
        }
    }

    @FXML
    private void enregistrerAnnonce() {
        String titre = titreField.getText();
        String description = descriptionField.getText();
        String voitureSelectionnee = voitureComboBox.getValue();

        if (titre.isEmpty() || description.isEmpty() || voitureSelectionnee == null) {
            afficherAlerte(Alert.AlertType.WARNING, "Veuillez remplir tous les champs.");
            return;
        }

        if (!voitureInfos.containsKey(voitureSelectionnee)) {
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors de la récupération de la voiture.");
            return;
        }

        int idVoiture = voitureInfos.get(voitureSelectionnee);

        Connection conn = MyDb.getInstance().getConnection();
        if (conn == null) {
            afficherAlerte(Alert.AlertType.ERROR, "Erreur de connexion à la base de données.");
            return;
        }

        try {
            PreparedStatement pstmt;
            if (annonceIdEnEdition != null) {
                String sql = "UPDATE annonce SET titre = ?, description = ?, voiture_id = ?, valider = 0 WHERE id = ?";
                pstmt = conn.prepareStatement(sql);
                pstmt.setString(1, titre);
                pstmt.setString(2, description);
                pstmt.setInt(3, idVoiture);
                pstmt.setInt(4, annonceIdEnEdition);
                
                pstmt.executeUpdate();
                afficherAlerte(Alert.AlertType.INFORMATION, "Annonce mise à jour avec succès !");
            } else {
                String sql = "INSERT INTO annonce (titre, description, voiture_id, valider) VALUES (?, ?, ?, 0)";
                pstmt = conn.prepareStatement(sql);
                pstmt.setString(1, titre);
                pstmt.setString(2, description);
                pstmt.setInt(3, idVoiture);
                
                pstmt.executeUpdate();
                afficherAlerte(Alert.AlertType.INFORMATION, "Annonce ajoutée avec succès !");
                
                sendEmailToAdmins(titre, description, voitureComboBox.getValue());
            }
            
            annonceIdEnEdition = null; 
            chargerAnnonces(); 
            formulairePane.setVisible(false);
            
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors de l'opération sur l'annonce: " + e.getMessage());
            afficherAlerte(Alert.AlertType.ERROR, 
                annonceIdEnEdition != null ? "Erreur lors de la mise à jour de l'annonce." 
                                         : "Erreur lors de l'ajout de l'annonce.");
        }
    }

    private void afficherDetailsAnnonce() {
        String selectedItem = listViewAnnonces.getSelectionModel().getSelectedItem();
        if (selectedItem == null) return;

        Connection conn = MyDb.getInstance().getConnection();
        String sql = "SELECT a.id, a.titre, a.description, " +
                    "v.id as voiture_id, v.marque, v.modele, v.annee, v.prix, v.image " +
                    "FROM annonce a " +
                    "JOIN voiture v ON a.voiture_id = v.id " +
                    "WHERE a.titre = ? AND v.marque = ? AND v.modele = ?";

        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            String[] parts = selectedItem.split(" \\(");
            String titre = parts[0];
            String[] voitureParts = parts[1].substring(0, parts[1].length() - 1).split(" ");
            String marque = voitureParts[0];
            String modele = voitureParts[1];

            pstmt.setString(1, titre);
            pstmt.setString(2, marque);
            pstmt.setString(3, modele);

            try (ResultSet rs = pstmt.executeQuery()) {
                if (rs.next()) {
                    detailsTitreLabel.setText(rs.getString("titre"));
                    detailsDescriptionText.setText(rs.getString("description"));
                    detailsMarqueLabel.setText("Marque: " + rs.getString("marque"));
                    detailsModeleLabel.setText("Modèle: " + rs.getString("modele"));
                    detailsAnneeLabel.setText("Année: " + rs.getInt("annee"));
                    detailsPrixLabel.setText(String.format("Prix: %.2f €", rs.getDouble("prix")));
                    
                    String imagePath = rs.getString("image");
                    if (imagePath != null && !imagePath.isEmpty()) {
                        Image image = new Image("file:" + imagePath, true);
                        detailsVoitureImageView.setImage(image);
                    } else {
                        detailsVoitureImageView.setImage(null);
                    }

                    formulairePane.setVisible(false);
                    detailsPane.setVisible(true);
                }
            }
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors du chargement des détails: " + e.getMessage());
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors du chargement des détails de l'annonce.");
        }
    }

    @FXML
    private void modifierAnnonce() {
        String selectedItem = listViewAnnonces.getSelectionModel().getSelectedItem();
        if (selectedItem == null) return;

        Connection conn = MyDb.getInstance().getConnection();
        String sql = "SELECT a.*, v.id as voiture_id " +
                    "FROM annonce a " +
                    "JOIN voiture v ON a.voiture_id = v.id " +
                    "WHERE a.titre = ?";

        try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
            String titre = selectedItem.split(" \\(")[0];
            pstmt.setString(1, titre);

            try (ResultSet rs = pstmt.executeQuery()) {
                if (rs.next()) {
                    annonceIdEnEdition = rs.getInt("id");
                    
                    titreField.setText(rs.getString("titre"));
                    descriptionField.setText(rs.getString("description"));
                    
                    int voitureId = rs.getInt("voiture_id");
                    for (Map.Entry<String, Integer> entry : voitureInfos.entrySet()) {
                        if (entry.getValue() == voitureId) {
                            voitureComboBox.setValue(entry.getKey());
                            break;
                        }
                    }

                    formTitleLabel.setText("Modifier l'annonce");
                    detailsPane.setVisible(false);
                    formulairePane.setVisible(true);
                }
            }
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors du chargement de l'annonce: " + e.getMessage());
            afficherAlerte(Alert.AlertType.ERROR, "Erreur lors du chargement de l'annonce pour modification.");
        }
    }

    @FXML
    private void supprimerAnnonce() {
        String selectedItem = listViewAnnonces.getSelectionModel().getSelectedItem();
        if (selectedItem == null) return;

        Alert confirmation = new Alert(Alert.AlertType.CONFIRMATION);
        confirmation.setTitle("Confirmation de suppression");
        confirmation.setHeaderText("Êtes-vous sûr de vouloir supprimer cette annonce ?");
        confirmation.setContentText("Cette action ne peut pas être annulée.");

        if (confirmation.showAndWait().get() == ButtonType.OK) {
            Connection conn = MyDb.getInstance().getConnection();
            String sql = "DELETE FROM annonce WHERE titre = ?";

            try (PreparedStatement pstmt = conn.prepareStatement(sql)) {
                String titre = selectedItem.split(" \\(")[0];
                pstmt.setString(1, titre);

                int affected = pstmt.executeUpdate();
                if (affected > 0) {
                    chargerAnnonces();
                    detailsPane.setVisible(false);
                    afficherAlerte(Alert.AlertType.INFORMATION, "Annonce supprimée avec succès.");
                }
            } catch (SQLException e) {
                System.err.println("❌ Erreur lors de la suppression: " + e.getMessage());
                afficherAlerte(Alert.AlertType.ERROR, "Erreur lors de la suppression de l'annonce.");
            }
        }
    }

    private void afficherAlerte(Alert.AlertType type, String message) {
        Alert alert = new Alert(type, message, ButtonType.OK);
        alert.showAndWait();
    }

    private List<String> getAdminEmails() {
        List<String> adminEmails = new ArrayList<>();
        Connection conn = MyDb.getInstance().getConnection();
        String sql = "SELECT email FROM user WHERE role = 'admin'";
        
        try (PreparedStatement pstmt = conn.prepareStatement(sql);
             ResultSet rs = pstmt.executeQuery()) {
            
            while (rs.next()) {
                adminEmails.add(rs.getString("email"));
            }
            
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors de la récupération des emails admin: " + e.getMessage());
        }
        
        return adminEmails;
    }

    private void sendEmailToAdmins(String annonceTitre, String annonceDescription, String voitureInfo) {
        List<String> adminEmails = getAdminEmails();
        if (adminEmails.isEmpty()) {
            System.out.println("⚠ Aucun administrateur trouvé pour envoyer l'email.");
            return;
        }

        final String from = "chaima.barhoumi@gmail.com";
        final String password = "lhparuaztzgyoipw";

        Properties props = new Properties();
        props.put("mail.smtp.host", "smtp.gmail.com");
        props.put("mail.smtp.port", "587");
        props.put("mail.smtp.auth", "true");
        props.put("mail.smtp.starttls.enable", "true");

        Session session = Session.getInstance(props, new jakarta.mail.Authenticator() {
            @Override
            protected PasswordAuthentication getPasswordAuthentication() {
                return new PasswordAuthentication(from, password);
            }
        });

        try {
            Message message = new MimeMessage(session);
            message.setFrom(new InternetAddress(from));
            
            // Ajouter tous les admins comme destinataires
            InternetAddress[] addresses = new InternetAddress[adminEmails.size()];
            for (int i = 0; i < adminEmails.size(); i++) {
                addresses[i] = new InternetAddress(adminEmails.get(i));
            }
            message.setRecipients(Message.RecipientType.TO, addresses);
            
            message.setSubject("Nouvelle annonce ajoutée: " + annonceTitre);
            message.setText("Une nouvelle annonce a été ajoutée:\n\n" +
                          "Titre: " + annonceTitre + "\n" +
                          "Description: " + annonceDescription + "\n" +
                          "Voiture: " + voitureInfo + "\n\n" +
                          "Veuillez vérifier et approuver si nécessaire.");

            Transport.send(message);
            System.out.println("✅ Email envoyé aux administrateurs.");
            
        } catch (MessagingException e) {
            System.err.println("❌ Erreur lors de l'envoi de l'email: " + e.getMessage());
        }
    }
}
