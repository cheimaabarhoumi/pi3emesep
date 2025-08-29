package Controllers;
import models.Voiture;
import javafx.scene.control.ListCell;
import javafx.scene.control.ListView;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.HBox;
import javafx.scene.text.Text;
import javafx.util.Callback;

public class VoitureListCell extends ListCell<Voiture> {
    private final HBox content = new HBox(10);
    private final ImageView imageView = new ImageView();
    private final Text detailsText = new Text();

    public VoitureListCell() {
        imageView.setFitWidth(50);  
        imageView.setFitHeight(50);
        content.getChildren().addAll(imageView, detailsText);
    }

    @Override
    protected void updateItem(Voiture voiture, boolean empty) {
        super.updateItem(voiture, empty);
        if (empty || voiture == null) {
            setGraphic(null);
        } else {
            Image image = new Image(getClass().getResourceAsStream("/images/" + voiture.getImage()));
            imageView.setImage(image);

            detailsText.setText(voiture.getMarque() + " " + voiture.getModele() + " (" + voiture.getAnnee() + ")");
            setGraphic(content);
        }
    }

    public static void applyToListView(ListView<Voiture> listView) {
        listView.setCellFactory(new Callback<>() {
            @Override
            public ListCell<Voiture> call(ListView<Voiture> param) {
                return new VoitureListCell();
            }
        });
    }
}
