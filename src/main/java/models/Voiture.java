package models;

public class Voiture {
    private int idVoiture;
    private String marque;
    private String modele;
    private int annee;
    private double prix;
    private int puissance_fiscale;
    private int kilometrage;
    private String boite_vitesse;
    private String carburant;
    private String cylindree;
    private int nombre_portes;
    private String image;

    public Voiture(int idVoiture, String marque, String modele, int annee, double prix, int puissance_fiscale,
                   int kilometrage, String boite_vitesse, String carburant, String cylindree,
                   int nombre_portes, String image) {
        this.idVoiture = idVoiture;
        this.marque = marque;
        this.modele = modele;
        this.annee = annee;
        this.prix = prix;
        this.puissance_fiscale = puissance_fiscale;
        this.kilometrage = kilometrage;
        this.boite_vitesse = boite_vitesse;
        this.carburant = carburant;
        this.cylindree = cylindree;
        this.nombre_portes = nombre_portes;
        this.image = (image == null || image.isEmpty()) ? "default.jpg" : image; // ✅ Gestion de l'image par défaut
    }

    public Voiture(String marque, String modele, int annee, double prix, int puissance_fiscale,
                   int kilometrage, String boite_vitesse, String carburant, String cylindree,
                   int nombre_portes, String image) {
        this(0, marque, modele, annee, prix, puissance_fiscale, kilometrage, boite_vitesse, carburant, cylindree, nombre_portes, image);
    }

    public int getIdVoiture() { return idVoiture; }
    public void setIdVoiture(int idVoiture) { this.idVoiture = idVoiture; }

    public String getMarque() { return marque; }
    public void setMarque(String marque) { this.marque = marque; }

    public String getModele() { return modele; }
    public void setModele(String modele) { this.modele = modele; }

    public int getAnnee() { return annee; }
    public void setAnnee(int annee) {
        if (annee < 1886 || annee > java.time.Year.now().getValue()) {
            throw new IllegalArgumentException("Année invalide !");
        }
        this.annee = annee;
    }

    public double getPrix() { return prix; }
    public void setPrix(double prix) {
        if (prix < 0) throw new IllegalArgumentException("Le prix ne peut pas être négatif !");
        this.prix = prix;
    }

    public int getpuissance_fiscale() { return puissance_fiscale; }
    public void setpuissance_fiscale(int puissance_fiscale) { this.puissance_fiscale = puissance_fiscale; }

    public int getKilometrage() { return kilometrage; }
    public void setKilometrage(int kilometrage) { this.kilometrage = kilometrage; }

    public String getboite_vitesse() { return boite_vitesse; }
    public void setboite_vitesse(String boite_vitesse) { this.boite_vitesse = boite_vitesse; }

    public String getCarburant() { return carburant; }
    public void setCarburant(String carburant) { this.carburant = carburant; }

    public String getCylindree() { return cylindree; }
    public void setCylindree(String cylindree) { this.cylindree = cylindree; }

    public int getnombre_portes() { return nombre_portes; }
    public void setnombre_portes(int nombre_portes) { this.nombre_portes = nombre_portes; }

    public String getImage() { return image; }
    public void setImage(String image) {
        this.image = (image == null || image.isEmpty()) ? "default.jpg" : image;
    }

    @Override
    public String toString() {
        return "Voiture{" +
                "idVoiture=" + idVoiture +
                ", marque='" + marque + '\'' +
                ", modele='" + modele + '\'' +
                ", annee=" + annee +
                ", prix=" + prix +
                ", puissance_fiscale=" + puissance_fiscale +
                ", kilometrage=" + kilometrage +
                ", boite_vitesse='" + boite_vitesse + '\'' +
                ", carburant='" + carburant + '\'' +
                ", cylindree='" + cylindree + '\'' +
                ", nombre_portes=" + nombre_portes +
                ", image='" + image + '\'' +
                '}';
    }
}
