package services;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import models.Annonce;
import utils.MyDb;

public class ServiceAnnonce implements IAnnonce <Annonce> {
    private List<Annonce> Annonces = new ArrayList<>();
    private final Connection cnx = MyDb.getInstance().getConnection();
    @Override
    public void create(Annonce obj) throws Exception {
        String sql = "INSERT INTO Annonce (id, titre, description, voiture_id, valider) VALUES (?, ?, ?, ?, ?)";

        try (PreparedStatement pstmt = cnx.prepareStatement(sql)) {
            pstmt.setInt(1, obj.getIdAnnonce());
            pstmt.setString(2, obj.getTitre());
            pstmt.setString(3, obj.getDescription());
            pstmt.setInt(4, obj.getVoitureId());
            pstmt.setInt(5, obj.getIsValidated());

            pstmt.executeUpdate();  
            Annonces.add(obj);       
            System.out.println("Annonce ajoutée: " + obj.getTitre() + " " + obj.getDescription());
            System.out.println("Annonce ajoutée: " +
                    "ID: " + obj.getIdAnnonce() + ", " +
                    "Titre: " + obj.getTitre() + ", " +
                    "Description: " + obj.getDescription() + ", " +
                    "Voiture ID: " + obj.getVoitureId() + ", " +
                    "Is Validated: " + obj.getIsValidated());
        }
    }




    public boolean voitureExists(int voitureId) {
        String query = "SELECT COUNT(*) FROM voiture WHERE id = ?";
        try (
                Connection conn = MyDb.getInstance().getConnection();
                PreparedStatement stmt = conn.prepareStatement(query)) {
            stmt.setInt(1, voitureId);
            ResultSet rs = stmt.executeQuery();
            rs.next();
            return rs.getInt(1) > 0;
        } catch (SQLException e) {
            e.printStackTrace();
            return false;
        }
    }


    @Override
    public void update(int idAnnonce, Annonce obj) throws Exception {
        String sql = "UPDATE Annonce SET titre = ?, description = ?, voiture_id = ?, valider = ? WHERE id = ?";

        try (PreparedStatement pstmt = cnx.prepareStatement(sql)) {
            pstmt.setString(1, obj.getTitre());
            pstmt.setString(2, obj.getDescription());
            pstmt.setInt(3, obj.getVoitureId());
            pstmt.setInt(4, obj.getIsValidated());
            pstmt.setInt(5, idAnnonce);

            int rowsAffected = pstmt.executeUpdate();
            if (rowsAffected > 0) {
                System.out.println("Annonce with ID " + idAnnonce + " has been updated.");
            } else {
                System.out.println("Annonce with ID " + idAnnonce + " not found.");
            }
        } catch (SQLException e) {
            System.out.println("Error while updating Annonce: " + e.getMessage());
            throw new Exception("Failed to update Annonce", e);
        }
    }


    @Override
    public void delete(int idAnnonce) throws Exception {
        String sql = "DELETE FROM Annonce WHERE id = ?";

        try (PreparedStatement pstmt = cnx.prepareStatement(sql)) {
            pstmt.setInt(1, idAnnonce);
            int rowsAffected = pstmt.executeUpdate();

            if (rowsAffected > 0) {
                Annonces.removeIf(annonce -> annonce.getIdAnnonce() == idAnnonce);
                System.out.println("Annonce supprimée avec ID: " + idAnnonce);
            } else {
                System.out.println("Aucune annonce trouvée avec l'ID: " + idAnnonce);
            }
        }
    }

    @Override
    public List<Annonce> getAll() throws Exception {
        return List.of();
    }


}

