package models;

public class Annonce {

    private int idAnnonce;
    private String titre;
    private String description;
    private int voitureId;
    private int valider; 

    public Annonce() {}

    public Annonce(int idAnnonce, String titre, String description, int voitureId) {
        this.idAnnonce = idAnnonce;
        this.titre = titre;
        this.description = description;
        this.voitureId = voitureId;
        this.valider = 0; 
    }

    public Annonce(int idAnnonce, String titre, String description, int voitureId, int valider) {
        this.idAnnonce = idAnnonce;
        this.titre = titre;
        this.description = description;
        this.voitureId = voitureId;
        this.valider = valider;
    }

    public int getIdAnnonce() { return idAnnonce; }
    public String getTitre() { return titre; }
    public String getDescription() { return description; }
    public int getVoitureId() { return voitureId; }
    public int getIsValidated() { return valider; }

    public void setId(int id) { this.idAnnonce = id; }
    public void setTitre(String titre) { this.titre = titre; }
    public void setDescription(String description) { this.description = description; }
    public void setVoitureId(int voitureId) { this.voitureId = voitureId; }
    public void setIsValidated(int valider) { this.valider = valider; }

    @Override
    public String toString() {
        return "Annonce{" +
                "idAnnonce=" + idAnnonce +
                ", titre='" + titre + '\'' +
                ", description='" + description + '\'' +
                ", voitureId=" + voitureId +
                ", valider=" + valider +
                '}';
    }
}
