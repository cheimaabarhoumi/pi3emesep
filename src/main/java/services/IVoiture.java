package services;
import java.util.List;

import models.Voiture;

public interface IVoiture <T>{


    void create(Voiture obj) throws Exception;
    void update(int idVoiture, Voiture obj) throws Exception;
    void delete(Voiture voiture) throws Exception;
    List<Voiture> getAll() throws Exception;
}
