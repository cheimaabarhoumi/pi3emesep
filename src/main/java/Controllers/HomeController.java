package Controllers;

import java.io.IOException;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.stage.Stage;

public class HomeController {
    @FXML private Button SuiviSponsoringButton;
   @FXML private Button SponsoringButton;
    @FXML private Button MessageButton;
    @FXML private Button TicketButton;
    @FXML
    private Button logoutButton;
    @FXML
    private Button signupButton;
    @FXML
    private Button loginButton;

    private boolean isLoggedIn = false; 

    @FXML
    public void initialize() {
        updateUI();
    }

    private void updateUI() {
        logoutButton.setVisible(isLoggedIn); 
        signupButton.setVisible(!isLoggedIn); 
        loginButton.setVisible(!isLoggedIn); 
    }

    @FXML
    private void handleLogout() {
        isLoggedIn = false;  
        updateUI(); 
    }

    @FXML
    private void goToSignup() throws IOException {
        changeScene("/fxml/signup.fxml");
    }

    @FXML
    private void goToLogin() throws IOException {
        isLoggedIn = true;
        updateUI(); 
        changeScene("/fxml/login.fxml");
    }

    private void changeScene(String fxmlFile) throws IOException {
        Stage stage = (Stage) signupButton.getScene().getWindow();
        FXMLLoader loader = new FXMLLoader(getClass().getResource(fxmlFile));
        Scene scene = new Scene(loader.load());
        stage.setScene(scene);
    }
    @FXML
    private void goToTicket() throws IOException {
        isLoggedIn = true;
        updateUI();  
        changeScene("/fxml/Tickets.fxml");
    }
    @FXML
    private void goToMessage() throws IOException {
        isLoggedIn = true;
        updateUI();  
        changeScene("/fxml/Messagerie.fxml");
    }
    @FXML
    private void goToSponsoring() throws IOException {
        isLoggedIn = true;
        updateUI(); 
        changeScene("/fxml/SponsoringView.fxml");
    }
    @FXML
    private void goToSuiviSponsoring() throws IOException {
        isLoggedIn = true;
        updateUI();  
        changeScene("/fxml/SuiviSponsoringView.fxml");
    }

}
