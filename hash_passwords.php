<?php
/**
 * SCRIPT DE ACTUALIZACIÓN DE CONTRASEÑAS
 * Convierte contraseñas plain text a BCRYPT
 * 
 * USO:
 * 1. Coloca este archivo en el servidor
 * 2. Accede a través del navegador: http://localhost/tienda_relojes/hash_passwords.php
 * 3. Verifica que se actualizaron correctamente
 * 4. Elimina este archivo del servidor
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cronos";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "<h1>🔐 Actualizador de Contraseñas a BCRYPT</h1>";
echo "<hr>";

// Obtener usuarios con contraseñas no hasheadas
$sql = "SELECT Clave_Usuario, Nombre_Usuario, Correo_Electronico, Contraseña 
        FROM Usuario 
        WHERE Contraseña NOT LIKE '$2y$%' 
        AND Contraseña NOT LIKE '$2a$%'
        AND Contraseña NOT LIKE '$2x$%'";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "<p style='color: green; font-size: 18px;'><strong>✓ Excelente!</strong> Todas las contraseñas ya están hasheadas con BCRYPT.</p>";
    echo "<p><strong>Usuarios encontrados:</strong> " . $conn->query("SELECT COUNT(*) as count FROM Usuario")->fetch_assoc()['count'] . "</p>";
} else {
    echo "<p><strong>Encontrados " . $result->num_rows . " usuarios con contraseña en plain text.</strong></p>";
    echo "<p><em>Iniciando conversión...</em></p>";
    echo "<hr>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Usuario</th>";
    echo "<th>Email</th>";
    echo "<th>Contraseña (antes)</th>";
    echo "<th>Estado</th>";
    echo "</tr>";

    $actualizados = 0;
    $errores = 0;

    while ($row = $result->fetch_assoc()) {
        $id = $row['Clave_Usuario'];
        $usuario = $row['Nombre_Usuario'];
        $email = $row['Correo_Electronico'];
        $password_plain = $row['Contraseña'];

        // Hashear la contraseña
        $password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);

        // Actualizar en BD
        $sql_update = "UPDATE Usuario SET Contraseña = ? WHERE Clave_Usuario = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("si", $password_hashed, $id);

        if ($stmt->execute()) {
            echo "<tr>";
            echo "<td>$id</td>";
            echo "<td><strong>$usuario</strong></td>";
            echo "<td>$email</td>";
            echo "<td><code>$password_plain</code></td>";
            echo "<td style='color: green;'><strong>✓ Actualizado</strong></td>";
            echo "</tr>";
            $actualizados++;
        } else {
            echo "<tr>";
            echo "<td>$id</td>";
            echo "<td><strong>$usuario</strong></td>";
            echo "<td>$email</td>";
            echo "<td><code>$password_plain</code></td>";
            echo "<td style='color: red;'><strong>✗ Error: " . $stmt->error . "</strong></td>";
            echo "</tr>";
            $errores++;
        }
    }

    echo "</table>";
    echo "<hr>";
    echo "<h2>Resumen:</h2>";
    echo "<p><strong style='color: green;'>Actualizados correctamente: $actualizados</strong></p>";
    if ($errores > 0) {
        echo "<p><strong style='color: red;'>Errores: $errores</strong></p>";
    }

    // Verificación final
    echo "<hr>";
    echo "<h2>Verificación Final:</h2>";
    $sql_verify = "SELECT COUNT(*) as count FROM Usuario WHERE Contraseña LIKE '$2y$%' OR Contraseña LIKE '$2a$%' OR Contraseña LIKE '$2x$%'";
    $result_verify = $conn->query($sql_verify);
    $row_verify = $result_verify->fetch_assoc();
    
    echo "<p><strong>Usuarios con BCRYPT:</strong> " . $row_verify['count'] . "</p>";
    
    $sql_plain = "SELECT COUNT(*) as count FROM Usuario WHERE Contraseña NOT LIKE '$2y$%' AND Contraseña NOT LIKE '$2a$%' AND Contraseña NOT LIKE '$2x$%'";
    $result_plain = $conn->query($sql_plain);
    $row_plain = $result_plain->fetch_assoc();
    
    echo "<p><strong>Usuarios con plain text:</strong> " . $row_plain['count'] . "</p>";

    if ($row_plain['count'] === 0) {
        echo "<p style='color: green; font-size: 16px;'><strong>✓ ¡Actualización completada exitosamente!</strong></p>";
    }
}

// Mostrar prueba de un usuario
echo "<hr>";
echo "<h2>🧪 Prueba Manual:</h2>";

$sql_test = "SELECT Nombre_Usuario, Contraseña FROM Usuario LIMIT 1";
$result_test = $conn->query($sql_test);

if ($result_test->num_rows > 0) {
    $row_test = $result_test->fetch_assoc();
    echo "<p><strong>Prueba con usuario:</strong> " . $row_test['Nombre_Usuario'] . "</p>";
    echo "<p><strong>Contraseña en BD:</strong> <code>" . substr($row_test['Contraseña'], 0, 50) . "...</code></p>";
    echo "<p><strong>¿Es BCRYPT?</strong> " . (strpos($row_test['Contraseña'], '$2y$') === 0 ? "✓ SÍ" : "✗ NO") . "</p>";
}

$conn->close();

echo "<hr>";
echo "<p style='color: red;'><strong>⚠️ IMPORTANTE:</strong> Después de verificar que todo funcionó, elimina este archivo del servidor.</p>";
echo "<p><code>rm hash_passwords.php</code> (en Linux/Mac) o elimina manualmente en Windows</p>";
?>
