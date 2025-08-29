package services;
import models.Annonce;
import services.ServiceAnnonce;
import java.util.List;

public interface IAnnonce <T>{


    void create(Annonce obj) throws Exception;
    void update(int idAnnonce, Annonce obj) throws Exception;



    void delete(int idAnnonce) throws Exception;
    List<Annonce> getAll() throws Exception;
}
