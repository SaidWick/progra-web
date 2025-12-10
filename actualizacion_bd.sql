-- ============================================================================
-- SCRIPT DE ACTUALIZACIÓN DE BASE DE DATOS - CRONOS
-- Sistema de Autenticación y Compras
-- ============================================================================
-- Ejecutar este script en orden para actualizar la BD existente

USE cronos;

-- ============================================================================
-- PASO 1: Actualizar tabla Usuario - Aumentar campo Contraseña
-- ============================================================================
-- BCRYPT genera hashes de ~60 caracteres, pero VARCHAR(255) es estándar

ALTER TABLE Usuario MODIFY Contraseña VARCHAR(255) NOT NULL;

-- ============================================================================
-- PASO 2: Verificar que existan los métodos de pago (pueden no estar)
-- ============================================================================

-- Si la tabla Metodo_Pago está vacía, insertar valores por defecto
INSERT IGNORE INTO Metodo_Pago (Clave_Metodo_Pago, Metodo_Pago) 
VALUES 
    (1, 'Tarjeta de Crédito'),
    (2, 'Tarjeta de Débito'),
    (3, 'PayPal');

-- ============================================================================
-- PASO 3: Validar estructura de tablas relacionadas
-- ============================================================================

-- Verificar tabla Compra (debe existir)
-- La tabla ya debería existir del boceto.sql original
-- Solo verificamos que tiene los campos correctos:

-- ALTER TABLE Compra MODIFY Clave_Metodo_Pago INT;  -- Si es necesario

-- ============================================================================
-- PASO 4: Verificar integridad referencial
-- ============================================================================

-- Verificar que no hay compras sin usuarios válidos
SELECT c.Clave_Compra, c.Clave_Usuario 
FROM Compra c 
LEFT JOIN Usuario u ON c.Clave_Usuario = u.Clave_Usuario 
WHERE u.Clave_Usuario IS NULL;

-- Si hay resultados, esas compras no pueden ser válidas
-- (eliminar manualmente si es necesario)

-- ============================================================================
-- PASO 5: Actualizar contraseñas existentes si están en plain text
-- ============================================================================

-- IMPORTANTE: Este paso se debe hacer SOLO si hay usuarios con contraseña
-- en plain text. Esto requiere un script PHP personalizado.
-- 
-- Ver archivo: scripts/hash_existing_passwords.php

-- ============================================================================
-- PASO 6: Crear índices para mejorar rendimiento (OPCIONAL)
-- ============================================================================

-- Índice para búsquedas de usuario por nombre
ALTER TABLE Usuario ADD INDEX idx_nombre_usuario (Nombre_Usuario);

-- Índice para búsquedas de usuario por email
ALTER TABLE Usuario ADD INDEX idx_correo (Correo_Electronico);

-- Índice para búsquedas de compras por usuario
ALTER TABLE Compra ADD INDEX idx_clave_usuario (Clave_Usuario);

-- ============================================================================
-- PASO 7: Verificar que la estructura está completa
-- ============================================================================

-- Mostrar estructura actual de Usuario
DESC Usuario;

-- ============================================================================
-- CONFIRMACIÓN
-- ============================================================================

-- Si todos los pasos completaron sin errores, la BD está lista para:
-- ✓ Registro de nuevos usuarios con contraseñas hasheadas
-- ✓ Login seguro
-- ✓ Persistencia de compras

SELECT 'Base de datos actualizada correctamente' AS Status;

-- ============================================================================
-- FIN DEL SCRIPT
-- ============================================================================
