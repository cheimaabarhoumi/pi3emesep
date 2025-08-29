package utils;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class MyDb {
    private Connection conn;
    private final String URL = "jdbc:mysql://localhost:3306/projectt";
    private final String USER = "root";
    private final String PASSWORD = "";
    private static MyDb instance;

    private MyDb() {
        try {
            conn = DriverManager.getConnection(URL, USER, PASSWORD);
            System.out.println("Connected to database");
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }

    }

    public static MyDb getInstance() {
        if (instance == null) {
            instance = new MyDb();
        }
        return instance;
    }

    public Connection getConnection() {
        try {
            if (conn == null || conn.isClosed()) {
                conn = DriverManager.getConnection(URL, USER, PASSWORD);
                System.out.println("✅ Nouvelle connexion établie à la base de données");
            }
        } catch (SQLException e) {
            System.err.println("❌ Erreur lors de la connexion à la base de données: " + e.getMessage());
        }
        return conn;
    }
    public void closeConnection() {
        try {
            if (conn != null && !conn.isClosed()) {
                conn.close();
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

}
