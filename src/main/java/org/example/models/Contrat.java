package org.example.models;

import java.time.LocalDate;
import java.time.LocalDateTime;

public class Contrat {
    private int idContrat;
    private int client_id;
    private int utilisateur_id;
    private String reference;
    private String intitule;
    private LocalDate date_debut;
    private LocalDate date_fin;
    private String statut;
    private double montant;
    private String frequence_paiement;
    private String mode_paiement;
    private String description;
    private String fichier_contrat;
    private String clauses_particulieres;
    private LocalDate date_signature;
    private boolean renouvellement_automatique;
    private LocalDate date_prochaine_revision;
    private LocalDateTime created_at;
    private LocalDateTime updated_at;
    private int user_id;

    public Contrat() {
    }

    public Contrat(int idContrat, int client_id, int utilisateur_id, String reference, String intitule, LocalDate date_debut, LocalDate date_fin, String statut, double montant, String frequence_paiement, String mode_paiement, String description, String fichier_contrat, String clauses_particulieres, LocalDate date_signature, boolean renouvellement_automatique, LocalDate date_prochaine_revision, LocalDateTime created_at, LocalDateTime updated_at, int user_id) {
        this.idContrat = idContrat;
        this.client_id = client_id;
        this.utilisateur_id = utilisateur_id;
        this.reference = reference;
        this.intitule = intitule;
        this.date_debut = date_debut;
        this.date_fin = date_fin;
        this.statut = statut;
        this.montant = montant;
        this.frequence_paiement = frequence_paiement;
        this.mode_paiement = mode_paiement;
        this.description = description;
        this.fichier_contrat = fichier_contrat;
        this.clauses_particulieres = clauses_particulieres;
        this.date_signature = date_signature;
        this.renouvellement_automatique = renouvellement_automatique;
        this.date_prochaine_revision = date_prochaine_revision;
        this.created_at = created_at;
        this.updated_at = updated_at;
        this.user_id = user_id;
    }

    public int getIdContrat() {
        return idContrat;
    }

    public void setIdContrat(int idContrat) {
        this.idContrat = idContrat;
    }

    public int getClient_id() {
        return client_id;
    }

    public void setClient_id(int client_id) {
        this.client_id = client_id;
    }

    public int getUtilisateur_id() {
        return utilisateur_id;
    }

    public void setUtilisateur_id(int utilisateur_id) {
        this.utilisateur_id = utilisateur_id;
    }

    public String getReference() {
        return reference;
    }

    public void setReference(String reference) {
        this.reference = reference;
    }

    public String getIntitule() {
        return intitule;
    }

    public void setIntitule(String intitule) {
        this.intitule = intitule;
    }

    public LocalDate getDate_debut() {
        return date_debut;
    }

    public void setDate_debut(LocalDate date_debut) {
        this.date_debut = date_debut;
    }

    public LocalDate getDate_fin() {
        return date_fin;
    }

    public void setDate_fin(LocalDate date_fin) {
        this.date_fin = date_fin;
    }

    public String getStatut() {
        return statut;
    }

    public void setStatut(String statut) {
        this.statut = statut;
    }

    public double getMontant() {
        return montant;
    }

    public void setMontant(double montant) {
        this.montant = montant;
    }

    public String getFrequence_paiement() {
        return frequence_paiement;
    }

    public void setFrequence_paiement(String frequence_paiement) {
        this.frequence_paiement = frequence_paiement;
    }

    public String getMode_paiement() {
        return mode_paiement;
    }

    public void setMode_paiement(String mode_paiement) {
        this.mode_paiement = mode_paiement;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getFichier_contrat() {
        return fichier_contrat;
    }

    public void setFichier_contrat(String fichier_contrat) {
        this.fichier_contrat = fichier_contrat;
    }

    public String getClauses_particulieres() {
        return clauses_particulieres;
    }

    public void setClauses_particulieres(String clauses_particulieres) {
        this.clauses_particulieres = clauses_particulieres;
    }

    public LocalDate getDate_signature() {
        return date_signature;
    }

    public void setDate_signature(LocalDate date_signature) {
        this.date_signature = date_signature;
    }

    public boolean isRenouvellement_automatique() {
        return renouvellement_automatique;
    }

    public void setRenouvellement_automatique(boolean renouvellement_automatique) {
        this.renouvellement_automatique = renouvellement_automatique;
    }

    public LocalDate getDate_prochaine_revision() {
        return date_prochaine_revision;
    }

    public void setDate_prochaine_revision(LocalDate date_prochaine_revision) {
        this.date_prochaine_revision = date_prochaine_revision;
    }

    public LocalDateTime getCreated_at() {
        return created_at;
    }

    public void setCreated_at(LocalDateTime created_at) {
        this.created_at = created_at;
    }

    public LocalDateTime getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(LocalDateTime updated_at) {
        this.updated_at = updated_at;
    }

    public int getUser_id() {
        return user_id;
    }

    public void setUser_id(int user_id) {
        this.user_id = user_id;
    }

    @Override
    public String toString() {
        return "Contrat{" +
                "idContrat=" + idContrat +
                ", reference='" + reference + '\'' +
                ", intitule='" + intitule + '\'' +
                ", date_debut=" + date_debut +
                ", date_fin=" + date_fin +
                ", statut='" + statut + '\'' +
                ", montant=" + montant +
                '}';
    }
}
