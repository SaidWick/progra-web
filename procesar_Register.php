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
$correo = $_POST['correo'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$password_input = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
$genero = $_POST['genero'] ?? '';

// Validar que los campos no estén vacíos
if (empty($nombre) || empty($password_input) || empty($correo) || empty($direccion) || empty($telefono)) {
    header("Location: register.html?error=campos_vacios");
    exit;
}

// Validar que las contraseñas coincidan
if ($password_input !== $password_confirm) {
    header("Location: register.html?error=contraseñas_no_coinciden");
    exit;
}

// Validar que el correo sea válido
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: register.html?error=correo_invalido");
    exit;
}

// Encriptar la contraseña
$password_hashed = password_hash($password_input, PASSWORD_BCRYPT);

// Verificar si el usuario ya existe
$sql_check = "SELECT Clave_Usuario FROM Usuario WHERE Nombre_Usuario = ? OR Correo_Electronico = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $nombre, $correo);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    header("Location: register.html?error=usuario_existe");
    exit;
}

// Insertar el nuevo usuario
$sql = "INSERT INTO Usuario (Nombre_Usuario, Correo_Electronico, Contraseña, Direccion, Telefono, Fecha_Nacimiento, Genero) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $nombre, $correo, $password_hashed, $direccion, $telefono, $fecha_nacimiento, $genero);

if ($stmt->execute()) {
    // Registro exitoso - crear sesión automáticamente
    $_SESSION['usuario_id'] = $stmt->insert_id;
    $_SESSION['usuario_nombre'] = $nombre;
    
    // Redirigir al inicio
    header("Location: index.html");
    exit;
} else {
    header("Location: register.html?error=error_registro");
    exit;
}

$stmt->close();
$conn->close();
?>
