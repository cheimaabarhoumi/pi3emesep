package Controllers;

import javafx.fxml.FXML;
import javafx.scene.control.TextArea;
import javafx.scene.control.TextField;
import models.Annonce;

public class ModifierAnnonceController {
    @FXML
    private TextField titreField;
    
    @FXML
    private TextArea descriptionField;

    private Annonce annonce;

    public void initData(Annonce annonce) {
        this.annonce = annonce;
        titreField.setText(annonce.getTitre());
        descriptionField.setText(annonce.getDescription());
    }

    public Annonce getUpdatedAnnonce() {
        annonce.setTitre(titreField.getText());
        annonce.setDescription(descriptionField.getText());
        return annonce;
    }
}
