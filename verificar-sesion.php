<?php
session_start();

// Retornar información de sesión en JSON
header('Content-Type: application/json');

$response = [
    'autenticado' => isset($_SESSION['usuario_id']),
    'usuario_id' => $_SESSION['usuario_id'] ?? null,
    'usuario_nombre' => $_SESSION['usuario_nombre'] ?? null
];

echo json_encode($response);
?>
