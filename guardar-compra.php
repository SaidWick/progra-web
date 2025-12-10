<?php
session_start();
header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para realizar una compra', 'require_login' => true]);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cronos";
$port = 3306;

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión']));
}

// Obtener datos del POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Obtener el usuario de la sesión
$clave_usuario = $_SESSION['usuario_id'];

// Insertar la compra
$fecha_compra = $data['fecha'];
$total = floatval($data['total']);
$clave_metodo_pago = 1; // Método de pago por defecto

$sql_compra = "INSERT INTO Compra (Clave_Usuario, Clave_Metodo_Pago, Fecha_Compra, Total) 
               VALUES (?, ?, ?, ?)";

$stmt_compra = $conn->prepare($sql_compra);
$stmt_compra->bind_param("iiss", $clave_usuario, $clave_metodo_pago, $fecha_compra, $total);

if ($stmt_compra->execute()) {
    $clave_compra = $stmt_compra->insert_id;
    
    // Insertar detalles de la compra
    $items_insertados = 0;
    $sql_detalle = "INSERT INTO Compra_Detalles (Clave_Compra, Clave_Producto, Stock_Compra, Precio_Unitario) 
                    VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    
    foreach ($data['items'] as $item) {
        $clave_producto = intval($item['id']);
        $cantidad = intval($item['quantity']);
        $precio_unitario = floatval(str_replace('$', '', str_replace(',', '', $item['price'])));
        
        $stmt_detalle->bind_param("iiid", $clave_compra, $clave_producto, $cantidad, $precio_unitario);
        
        if ($stmt_detalle->execute()) {
            $items_insertados++;
        }
    }
    
    if ($items_insertados === count($data['items'])) {
        echo json_encode(['success' => true, 'message' => 'Compra guardada correctamente', 'compra_id' => $clave_compra]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar algunos detalles']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar la compra: ' . $stmt_compra->error]);
}

$conn->close();
?>
