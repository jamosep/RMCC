package com.rmcc.controllers;

import com.rmcc.utils.DBConnection;
import java.io.IOException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.net.URLEncoder;

@WebServlet("/Login")
public class LoginServlet extends HttpServlet {
    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response)
            throws ServletException, IOException {
        String username = request.getParameter("username");
        String password = request.getParameter("password");
        
        try (Connection conn = DBConnection.getConnection()) {
            String sql = "SELECT * FROM usuariosrmcc WHERE username = ? AND password = ?";
            PreparedStatement stmt = conn.prepareStatement(sql);
            stmt.setString(1, username);
            stmt.setString(2, password);
            
            ResultSet rs = stmt.executeQuery();
            
            if (rs.next()) {
                // Usuario encontrado - crear sesión
                HttpSession session = request.getSession();
                session.setAttribute("username", username);
                
                // Generar un token de sesión único
                String sessionToken = java.util.UUID.randomUUID().toString();
                session.setAttribute("sessionToken", sessionToken);
                
                // Guardar la URL de redirección final en la sesión
                String redirectUrl = "http://localhost:8000/Index.php?token=" + 
                    URLEncoder.encode(sessionToken, "UTF-8") + 
                    "&username=" + URLEncoder.encode(username, "UTF-8");
                session.setAttribute("finalRedirect", redirectUrl);
                
                // Redirigir primero a welcome.jsp
                response.sendRedirect("welcome.jsp");
            } else {
                // Usuario no encontrado o contraseña incorrecta
                request.setAttribute("error", "Usuario o contraseña incorrectos");
                request.getRequestDispatcher("login.jsp").forward(request, response);
            }
        } catch (SQLException e) {
            e.printStackTrace();
            request.setAttribute("error", "Error en el sistema");
            request.getRequestDispatcher("login.jsp").forward(request, response);
        }
    }
}