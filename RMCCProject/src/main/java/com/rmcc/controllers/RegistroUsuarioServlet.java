package com.rmcc.controllers;

import com.rmcc.utils.DBConnection;
import java.io.IOException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import javax.servlet.RequestDispatcher;

@WebServlet("/RegistroUsuario")
public class RegistroUsuarioServlet extends HttpServlet {

    private static final String ROL_ADMIN = "Admin"; // Exactamente como está en la base de datos
    private static final String ROL_USER = "user";   // Exactamente como está en la base de datos

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {

        System.out.println("Iniciando proceso de registro de usuario");

        request.setCharacterEncoding("UTF-8");
        String username = request.getParameter("username");
        String password = request.getParameter("password");
        String rol = request.getParameter("rol");

        // Debug para ver exactamente qué valor llega del formulario
        System.out.println("Valor del rol recibido del formulario: '" + rol + "'");

        // Validación específica del rol
        if (rol == null || rol.trim().isEmpty()) {
            System.out.println("ADVERTENCIA: Rol no especificado, usando valor por defecto 'user'");
            rol = ROL_USER;
        }

        // Validación exacta del rol sin modificar el caso
        if (!ROL_ADMIN.equals(rol) && !ROL_USER.equals(rol)) {
            System.out.println("Error: Rol no válido especificado: " + rol);
            request.setAttribute("error", "Rol no válido especificado");
            request.getRequestDispatcher("/registroUsuario.jsp").forward(request, response);
            return;
        }

        System.out.println("Rol validado: " + rol);

        // Validación básica
        if (username == null || username.trim().isEmpty()
                || password == null || password.trim().isEmpty()) {
            String errorMsg = "Username y password son requeridos";
            System.out.println("Error de validación: " + errorMsg);
            request.setAttribute("error", errorMsg);
            request.getRequestDispatcher("/registroUsuario.jsp").forward(request, response);
            return;
        }

        Connection conn = null;
        PreparedStatement stmt = null;
        PreparedStatement checkStmt = null;
        ResultSet rs = null;

        try {
            conn = DBConnection.getConnection();

            // Verificar si el usuario ya existe
            String checkSql = "SELECT username FROM usuariosrmcc WHERE username = ?";
            checkStmt = conn.prepareStatement(checkSql);
            checkStmt.setString(1, username);
            rs = checkStmt.executeQuery();

            if (rs.next()) {
                request.setAttribute("error", "El usuario '" + username + "' ya existe");
                request.getRequestDispatcher("/registroUsuario.jsp").forward(request, response);
                return;
            }

            // Insertar el nuevo usuario
            String sql = "INSERT INTO usuariosrmcc (username, password, rol) VALUES (?, ?, ?)";
            System.out.println("SQL de inserción con valores - username: " + username + ", rol: " + rol);

            stmt = conn.prepareStatement(sql);
            stmt.setString(1, username);
            stmt.setString(2, password);
            stmt.setString(3, rol);

            int result = stmt.executeUpdate();

            if (result > 0) {
                // Verificar inmediatamente que el rol se guardó correctamente
                String verifySql = "SELECT rol FROM usuariosrmcc WHERE username = ?";
                PreparedStatement verifyStmt = conn.prepareStatement(verifySql);
                verifyStmt.setString(1, username);
                ResultSet verifyRs = verifyStmt.executeQuery();

                if (verifyRs.next()) {
                    String rolGuardado = verifyRs.getString("rol");
                    System.out.println("ROL GUARDADO EN BD: '" + rolGuardado + "'");

                    if (!rol.equals(rolGuardado)) {
                        System.out.println("ADVERTENCIA: El rol guardado (" + rolGuardado
                                + ") no coincide con el rol enviado (" + rol + ")");
                    }
                }

                request.setAttribute("success", "Usuario registrado exitosamente: "
                        + username + " con rol: " + rol);
            } else {
                request.setAttribute("error", "No se pudo registrar el usuario");
            }

        } catch (SQLException e) {
            System.err.println("Error SQL: " + e.getMessage());
            request.setAttribute("error", "Error al registrar usuario: " + e.getMessage());

        } finally {
            try {
                if (rs != null) {
                    rs.close();
                }
                if (checkStmt != null) {
                    checkStmt.close();
                }
                if (stmt != null) {
                    stmt.close();
                }
                if (conn != null) {
                    conn.close();
                }
            } catch (SQLException e) {
                System.err.println("Error al cerrar recursos: " + e.getMessage());
            }

            request.getRequestDispatcher("/registroUsuario.jsp").forward(request, response);
        }
    }
}
