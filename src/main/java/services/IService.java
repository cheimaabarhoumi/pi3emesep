package services;


import java.util.List;

public interface IService<T> {
    void create(T obj) throws Exception;

    void update(T obj) throws Exception;

    void delete(int id) throws Exception;

    List<T> getAll() throws Exception;


}
