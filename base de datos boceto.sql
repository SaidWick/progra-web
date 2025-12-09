CREATE DATABASE cronos;
USE cronos;

CREATE TABLE Metodo_Pago(
	Clave_Metodo_Pago INT AUTO_INCREMENT,
    Metodo_Pago VARCHAR(25) NOT NULL,
    PRIMARY KEY(Clave_Metodo_Pago)
)
    
CREATE TABLE Usuario(
	Clave_Usuario INT AUTO_INCREMENT,
    Nombre_Usuario VARCHAR(50) NOT NULL,
    Correo_Electronico VARCHAR(50) NOT NULL UNIQUE,
    Contraseña VARCHAR(255) NOT NULL, -- Consejos para el hash de proteccion para contraseñas.
    Direccion VARCHAR(100) NOT NULL,
    Telefono VARCHAR(15) NOT NULL,
    Fecha_Nacimiento DATE,
    Genero CHAR(1),
    Fecha_Creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    Clave_Metodo_Pago INT,
    PRIMARY KEY(Clave_Usuario),
    FOREIGN KEY(Clave_Metodo_Pago) REFERENCES Metodo_Pago(Clave_Metodo_Pago)
)

CREATE TABLE Producto(
	Clave_Producto INT AUTO_INCREMENT,
    Nombre_Producto VARCHAR(50) NOT NULL,
    Descripcion VARCHAR(255) NOT NULL,
    Stock INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    PRIMARY KEY(Clave_Producto)
)

CREATE TABLE Favoritos(
	Clave_Favoritos INT AUTO_INCREMENT,
    Clave_Usuario INT,
    Clave_Producto INT,
    PRIMARY KEY(Clave_Favoritos),
    FOREIGN KEY(Clave_Usuario) REFERENCES Usuario(Clave_Usuario),
    FOREIGN KEY(Clave_Producto) REFERENCES Producto(Clave_Producto)
)

CREATE TABLE Compra(
	Clave_Compra INT AUTO_INCREMENT,
    Clave_Usuario INT,
    CLave_Metodo_Pago INT,
    Fecha_Compra DATE,
    Total DECIMAL(10, 2) NOT NULL
    PRIMARY KEY(Clave_Compra),
    FOREIGN KEY(Clave_Usuario) REFERENCES Usuario(Clave_Usuario),
    FOREIGN KEY(Clave_Metodo_Pago) REFERENCES Metodo_Pago(Clave_Metodo_Pago)
)

CREATE TABLE Compra_Detalles(
	Clave_Detalle INT AUTO_INCREMENT,
    Clave_Compra INT,
    Clave_Producto INT,
    Stock_Compra INT NOT NULL,
    Precio_Unitario DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY(Clave_Detalle),
    FOREIGN KEY(Clave_Compra) REFERENCES Compra(Clave_Compra),
    FOREIGN KEY(Clave_Producto) REFERENCES Producto(Clave_Producto)
)

CREATE TABLE Carrito(
	Clave_Carrito INT AUTO_INCREMENT,
    Clave_Usuario INT,
    Clave_Producto INT,
    Stock_Carrito INT NOT NULL,
    PRIMARY KEY(Clave_Carrito),
    FOREIGN KEY(Clave_Usuario) REFERENCES Usuario(Clave_Usuario),
    FOREIGN KEY(Clave_Producto) REFERENCES Producto(Clave_Producto)
)