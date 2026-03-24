<?php
session_start();
header('Content-Type: application/json'); // Siempre responderemos en JSON

// 1. Conexión (Asegúrate de que los datos sean correctos)
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sistema_sorteo";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'msg' => 'Error de conexión']);
    exit;
}

$action = $_POST['action'] ?? '';

// --- LÓGICA DE REGISTRO ---
if ($action === 'register') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $user   = $_POST['user'] ?? '';
    $pass   = $_POST['code'] ?? ''; 

    // CIFRADO DE CONTRASEÑA
    $passHash = password_hash($pass, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_completo, correo, usuario, codigo_especial) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $correo, $user, $passHash);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Error al registrar usuario']);
    }
    $stmt->close();
}

// --- LÓGICA DE LOGIN (El código que preguntaste) ---
elseif ($action === 'login') {
    $userOrEmail = $_POST['user'] ?? '';
    $passInput   = $_POST['code'] ?? '';

    $stmt = $conn->prepare("SELECT usuario, codigo_especial FROM usuarios WHERE usuario = ? OR correo = ?");
    $stmt->bind_param("ss", $userOrEmail, $userOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        // Verificamos el hash
        if (password_verify($passInput, $usuario['codigo_especial'])) {
            $_SESSION['usuario'] = $usuario['usuario']; // Guardamos en sesión de servidor por seguridad
            echo json_encode([
                'status' => 'success',
                'username' => $usuario['usuario']
            ]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Contraseña incorrecta']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Usuario no encontrado']);
    }
    $stmt->close();
}

$conn->close();