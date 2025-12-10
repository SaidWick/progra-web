<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cronos";
$port = 3306;

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos del formulario
$usuario = $_POST['usuario'] ?? '';
$password_input = $_POST['password'] ?? '';

// Validar que los campos no estén vacíos
if (empty($usuario) || empty($password_input)) {
    header("Location: login.html?error=campos_vacios");
    exit;
}

// Buscar el usuario en la BD
$sql = "SELECT Clave_Usuario, Nombre_Usuario, Contraseña FROM Usuario WHERE Nombre_Usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    
    // Verificar la contraseña
    if (password_verify($password_input, $row['Contraseña'])) {
        // Contraseña correcta - crear sesión
        $_SESSION['usuario_id'] = $row['Clave_Usuario'];
        $_SESSION['usuario_nombre'] = $row['Nombre_Usuario'];
        
        // Redirigir al inicio
        header("Location: index.html");
        exit;
    } else {
        // Contraseña incorrecta
        header("Location: login.html?error=credenciales_invalidas");
        exit;
    }
} else {
    // Usuario no encontrado
    header("Location: login.html?error=usuario_no_encontrado");
    exit;
}

$stmt->close();
$conn->close();
?>
