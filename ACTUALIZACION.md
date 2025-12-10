# Implementación - Autenticación y Compras

## Cambios en BD

```sql
-- Aumentar campo contraseña
ALTER TABLE Usuario MODIFY Contraseña VARCHAR(255);

-- Hashear contraseñas existentes (si están en plain text)
-- Ejecutar script: hash_passwords.php en navegador
```

## Archivos Nuevos a Copiar

```
procesar_login.php          - Login
procesar_Register.php       - Registro
verificar-sesion.php        - Verificar sesión activa
cerrar-sesion.php          - Logout
guardar-compra.php         - Guardar compra en BD
```

## Cambios en Archivos Existentes

```
accesorios.js              - Agregada función verificarSesion()
index.html                 - Agregado ID user-container
register.html              - Campos corregidos
accesorios.css             - Estilos actualizados
index.css                  - Estilos actualizados
```

## Cómo Funciona

1. Usuario se registra → `procesar_Register.php` hashea contraseña
2. Usuario hace login → `procesar_login.php` verifica con password_verify()
3. Se crea sesión `$_SESSION['usuario_id']`
4. JavaScript verifica sesión → muestra nombre o icono login
5. Usuario compra → `guardar-compra.php` guarda en BD si está autenticado

## Testing

- Login con: `Juan Perez` / `contrasena1`
- Agregar al carrito
- Hacer compra
- Verificar en BD tabla `Compra`
