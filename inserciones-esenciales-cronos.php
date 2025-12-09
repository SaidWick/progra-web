<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cronos";
$port = 3307;

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar la conexión
if ($conn->connect_error) {
    die("La conexión ha fallado: " . $conn->connect_error);
}

// Inserciones para registrar dos métodos de pago
$sql = "INSERT INTO Metodo_Pago (Metodo_Pago)
VALUES ('Tarjeta de Crédito'),
       ('Tarjeta de Débito'),
       ('PayPal')";
if ($conn->query($sql) === TRUE) {
    echo "Métodos de pago registrados exitosamente";
} else {
    echo "Error registrando métodos de pago: " . $conn->error;
}

// Inserciones para registrar dos usuarios
$sql = "INSERT INTO Usuario (Nombre_Usuario, Correo_Electronico, Contraseña, Direccion, Telefono, Fecha_Nacimiento, Genero, Clave_Metodo_Pago)
VALUES ('Juan Perez', 'juan.perez@example.com', 'contrasena1', 'Calle Falsa 123', '1234567890', '1990-01-01', 'M', 1),
       ('Maria Lopez', 'maria.lopez@example.com', 'contrasena2', 'Avenida Siempre Viva 742', '0987654321', '1985-05-10', 'F', 2)";

if ($conn->query($sql) === TRUE) {
    echo "Usuarios registrados exitosamente";
} else {
    echo "Error registrando usuarios: " . $conn->error;
}

// Inserciones para registrar varios productos
$sql = "INSERT INTO Producto (Nombre_Producto, Descripcion, Stock, Precio)
VALUES ('Rolex Submariner', 'El icónico reloj de buceo con un diseño robusto y elegante', 50, 10700.00),
       ('Rolex Lady-Datejust', 'Un clásico atemporal con un diseño elegante, perfecto para cualquier ocasion', 50, 7500.00),
       ('Seiko Prospex Diver', 'Reloj de buceo robusto y accesible para aventureros', 50, 9800.00),
       ('Omega Constellation', 'Reloj elegante con detalles en oro rosa y un diseño sofisticado', 50, 4200.00),
       ('Omega Speedmaster Moonwacth', 'El legendario reloj utilizado en buenos momentos', 50, 8100.00),
       ('Cartier Ballon Bleu', 'Elegancia y lujo en un diseño redondeado con el icónico cabujón rosa', 50, 14900.00),
       ('Cartier Santos', 'Elegancia atemporal y versatilidad con correas intercambiable', 50, 12500.00),
       ('Michael Kors Parker', 'Reloj moderno y brillante con detalles de cristales que lo hacen destacar', 50, 13300.00),
       ('Tissot Le Locle', 'Elegante reloj clásico con detalles fino y precisión suiza', 50, 16200.00),
       ('Seiko Prospex Second Edition', 'Reloj de buceo robusto y accesible para aventureros y mas', 50, 15300.00),
       ('Tag Hever Carrera', 'Combina elegancia y precisión con un diseño sofisticado y clásico', 50, 11800.00),
       ('Tag Hever Aquaracer Lady', 'Reloj elegante para mujeres activas que buscan estilo y funcionalidad', 50, 9800.00),
       ('Seiko Presage Cocktail Time', 'Inspirado en cócteles, ofrece un diseño clásico con detalles elegantes', 50, 10700.00),
       ('Lapso', 'Reloj moderno y audaz con un diseño innovador y materiales avanzados', 50, 11800.00),
       ('Breitling Navitimer', 'Un reloj icónico para los pilotos, con funciones avanzadas y un diseño clásico', 50, 7600.00),
       ('Anillo de Compromiso Tiffany Setting', 'Este icónico anillo de Tiffany & Co. presenta un diamante redondo, diseñado para maximizar el brillo y la luz', 50, 2100.00),
       ('Pulsera Cartier Love', 'Un diseño clásico, hecha de oro y perlas decorativas', 50, 1500.00),
       ('Collar Van Cleef & Arpels Alhambra', 'Un diseño icónico con motivos de trébol, está elaborado en oro amaril,lo con nácar', 50, 1250.00),
       ('Anillo Bulgari Serpenti', 'Inspirado en la serpiente, símbolo de seducción y elegancia', 50, 2900.00),
       ('Pulsera Pandora Moments', 'Personalizable y elegante, hecha de oro', 50, 2000.00),
       ('Collar Swarovski Attract', 'Elegante y moderno, con un brillante colgante de oroo que añade un toque de sofisticación a cualquier atuendo', 50, 1800.00)";

if ($conn->query($sql) === TRUE) {
    echo "Productos registrados exitosamente";
} else {
    echo "Error registrando productos: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
