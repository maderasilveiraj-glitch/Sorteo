<?php
// 1. Configuración de la Base de Datos
$host = "localhost";
$user = "root";          // Usuario por defecto de XAMPP
$pass = "";              // Contraseña por defecto (vacía)
$db   = "sistema_rifa";  // Asegúrate de que este nombre exista en phpMyAdmin

// 2. Crear la conexión
$conexion = new mysqli($host, $user, $pass, $db);

// 3. Verificar si la conexión falló
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 4. Configuración de Administración
$ADMIN_USER = "admin";
$ADMIN_PASS = "Suerte2026"; 

// 5. Función de sesión
function estaLogueado() {
    return isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;
}
?>