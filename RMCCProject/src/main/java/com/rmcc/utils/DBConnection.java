package com.rmcc.utils;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class DBConnection {
    // Actualiza estos valores según tu configuración
    private static final String URL = "jdbc:mysql://localhost:3306/users?useSSL=false&serverTimezone=UTC";
    private static final String USER = "root";
    private static final String PASSWORD = "";

    static {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            System.out.println("Driver MySQL cargado exitosamente");
        } catch (ClassNotFoundException e) {
            System.err.println("Error al cargar el driver MySQL: " + e.getMessage());
            e.printStackTrace();
            throw new RuntimeException("Error al cargar el driver MySQL", e);
        }
    }

    public static Connection getConnection() throws SQLException {
        System.out.println("Intentando conectar a: " + URL);
        try {
            Connection conn = DriverManager.getConnection(URL, USER, PASSWORD);
            if (conn != null) {
                System.out.println("Conexión a la base de datos establecida exitosamente");
                return conn;
            } else {
                System.err.println("La conexión retornó null");
                throw new SQLException("No se pudo establecer la conexión");
            }
        } catch (SQLException e) {
            System.err.println("Error al conectar a la base de datos:");
            System.err.println("URL: " + URL);
            System.err.println("Usuario: " + USER);
            System.err.println("Error: " + e.getMessage());
            System.err.println("Código de error: " + e.getErrorCode());
            System.err.println("Estado SQL: " + e.getSQLState());
            e.printStackTrace();
            throw e;
        }
    }
}