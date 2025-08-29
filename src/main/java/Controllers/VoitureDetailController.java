package Controllers;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import models.Voiture;

public class VoitureDetailController {
    @FXML
    private Label lblMarque;
    @FXML
    private Label lblModele;
    @FXML
    private Label lblAnnee;
    @FXML
    private Label lblPrix;
    @FXML
    private Label lblKilometrage;
    @FXML
    private ImageView imageViewVoiture;

    private Voiture voiture;

    public void initialize() {
    }

    public void setVoiture(Voiture voiture) {
        this.voiture = voiture;
        displayVoitureDetails();
    }

    private void displayVoitureDetails() {
        if (voiture != null) {
            lblMarque.setText("Marque: " + voiture.getMarque());
            lblModele.setText("Modèle: " + voiture.getModele());
            lblAnnee.setText("Année: " + voiture.getAnnee());
            lblPrix.setText("Prix: " + voiture.getPrix() + " Tnd");
            lblKilometrage.setText("Kilométrage: " + voiture.getKilometrage() + " km");

            if (voiture.getImage() != null && !voiture.getImage().isEmpty()) {
                try {
                    Image image = new Image("file:" + voiture.getImage(), true);
                    imageViewVoiture.setImage(image);
                } catch (Exception e) {
                    System.out.println("❌ Erreur lors du chargement de l'image: " + e.getMessage());
                    imageViewVoiture.setImage(null);
                }
            }
        }
    }
}
