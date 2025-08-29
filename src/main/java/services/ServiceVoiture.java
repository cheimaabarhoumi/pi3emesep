package services;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

import models.Voiture;
import utils.MyDb;

public class ServiceVoiture implements IVoiture<Voiture> {
    private List<Voiture> voitures = new ArrayList<>();
    private Connection cnx = MyDb.getInstance().getConnection();

    @Override
    public void create(Voiture obj) throws Exception {
        String sql = "INSERT INTO voiture (marque, modele, annee, prix, puissance_fiscale, kilometrage, boite_vitesse, carburant, cylindree, nombre_portes, image) " +
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";

        try (PreparedStatement pstmt = cnx.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            pstmt.setString(1, obj.getMarque());
            pstmt.setString(2, obj.getModele());
            pstmt.setInt(3, obj.getAnnee());
            pstmt.setDouble(4, obj.getPrix());
            pstmt.setInt(5, obj.getpuissance_fiscale());
            pstmt.setInt(6, obj.getKilometrage());
            pstmt.setString(7, obj.getboite_vitesse());
            pstmt.setString(8, obj.getCarburant());
            pstmt.setString(9, obj.getCylindree());
            pstmt.setInt(10, obj.getnombre_portes());
            pstmt.setString(11, obj.getImage());



            int affectedRows = pstmt.executeUpdate();
            if (affectedRows > 0) {
                ResultSet generatedKeys = pstmt.getGeneratedKeys();
                if (generatedKeys.next()) {
                    obj.setIdVoiture(generatedKeys.getInt(1));
                }
                voitures.add(obj);
                System.out.println("✅ Voiture ajoutée : " + obj);
            } else {
                throw new Exception("L'ajout de la voiture a échoué, aucune ligne affectée.");
            }
        } catch (SQLException e) {
            System.out.println("❌ Erreur SQL lors de l'ajout : " + e.getMessage());
        }
    }


    @Override
    public void update(int idVoiture, Voiture obj) throws Exception {
        String sql = "UPDATE voiture SET marque = ?, modele = ?, annee = ?, prix = ?, puissance_fiscale = ?, kilometrage = ?, " +
                "boite_vitesse = ?, carburant = ?, cylindree = ?, nombre_portes = ?, image = ? WHERE id = ?";

        try (PreparedStatement pstmt = cnx.prepareStatement(sql)) {
            pstmt.setString(1, obj.getMarque());
            pstmt.setString(2, obj.getModele());
            pstmt.setInt(3, obj.getAnnee());
            pstmt.setDouble(4, obj.getPrix());
            pstmt.setInt(5, obj.getpuissance_fiscale());
            pstmt.setInt(6, obj.getKilometrage());
            pstmt.setString(7, obj.getboite_vitesse());
            pstmt.setString(8, obj.getCarburant());
            pstmt.setString(9, obj.getCylindree());
            pstmt.setInt(10, obj.getnombre_portes());
            pstmt.setString(11, obj.getImage());
            pstmt.setInt(12, idVoiture);

            int rowsUpdated = pstmt.executeUpdate();
            if (rowsUpdated > 0) {
                for (int i = 0; i < voitures.size(); i++) {
                    if (voitures.get(i).getIdVoiture() == idVoiture) {
                        voitures.set(i, obj);
                        break;
                    }
                }
                System.out.println("✅ Voiture mise à jour : " + obj);
            } else {
                System.out.println("❌ Aucune voiture trouvée avec ID : " + idVoiture);
            }
        } catch (SQLException e) {
            System.out.println("❌ Erreur SQL lors de la mise à jour : " + e.getMessage());
        }
    }

    public void delete(Voiture voiture) {
        String sql = "DELETE FROM voiture WHERE id = ?";

        try (PreparedStatement pstmt = cnx.prepareStatement(sql)) {
            pstmt.setInt(1, voiture.getIdVoiture());

            int rowsAffected = pstmt.executeUpdate();
            if (rowsAffected > 0) {
                voitures.removeIf(v -> v.getIdVoiture() == voiture.getIdVoiture());
                System.out.println("✅ Voiture supprimée : " + voiture);
            } else {
                System.out.println("❌ Aucune voiture trouvée avec l'ID : " + voiture.getIdVoiture());
            }
        } catch (SQLException e) {
            System.out.println("❌ Erreur SQL lors de la suppression : " + e.getMessage());
        }
    }


    @Override
    public List<Voiture> getAll() {
        List<Voiture> voitures = new ArrayList<>();
        String query = "SELECT * FROM voiture"; 

        try (PreparedStatement ps = cnx.prepareStatement(query);
             ResultSet rs = ps.executeQuery()) {

            while (rs.next()) {
                Voiture voiture = new Voiture(
                        rs.getInt("id"),
                        rs.getString("marque"),
                        rs.getString("modele"),
                        rs.getInt("annee"),
                        rs.getDouble("prix"),
                        rs.getInt("puissance_fiscale"),
                        rs.getInt("kilometrage"),
                        rs.getString("boite_vitesse"),
                        rs.getString("carburant"),
                        rs.getString("cylindree"),
                        rs.getInt("nombre_portes"),
                        rs.getString("image")
                );
                voitures.add(voiture);
            }
        } catch (SQLException e) {
            System.out.println("❌ Erreur SQL lors de la récupération des voitures : " + e.getMessage());
        }
        return voitures;
    }


}
