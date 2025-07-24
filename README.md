# ModularINCO2025
Proyecto modular

/* Estos serian los pasos a seguir: */
1- Descargar lo que hay en rama "Victor"
2- Abrir el proyecto
3- Abrir una terminal en carpeta raíz del proyecto -> public, y colocar el siguiente comando php -S localhost:3000, esto hara levantar el proyecto para cuando accedan a local, igual dejo mi ejemplo:
PS C:\Users\LENOVO\documents\MODULARINCO2025\public> php -S localhost:3000
4- Abrir otra terminal dentro de la carpeta raiz del proyecto y colocar " npm run start " esto hace compilar el sass que utilizamos para estilos
5- Abrir de nuevo una terminal para levantar el puerto para el websocket y colocar el siguiente comando " node app.js "
6- En el archivo database.php colocar sus credenciales de la DB
7- Si van hacer pruebas de registro o recuperacion de contraseña para que funcione y les lleguen los enlaces, hay que crear cuenta en mailtrap y modificar el archivo Email.php de acuerdo a sus credenciales

/*** Estos son todos los querys que he utilizado ****/
use constructora;

SELECT * FROM mensajes_chat;
SELECT * FROM manifiestos;
SELECT * FROM unidades_de_transporte;

INSERT INTO productos (descripcion, precio) VALUES ('Escombro', '1234');

-- Cambiar el nombre de la columna idmaterial a idproducto y agregar AUTO_INCREMENT
ALTER TABLE constructora.productos 
CHANGE idmaterial idproducto INT NOT NULL;

-- Insertar datos en la tabla productos
INSERT INTO constructora.productos (descripcion, precio)
VALUES ('Arena fina', 1250.50);


-- Establecer idproducto como la clave primaria
ALTER TABLE constructora.productos 
ADD PRIMARY KEY (idproducto);

/*Elimina todas las filas de una vez. También reinicia los valores de las columnas auto-incrementables*/
TRUNCATE TABLE unidades_de_transporte;

ALTER TABLE unidades_de_transporte RENAME COLUMN idempleado TO chofer;

/* Haciendo admin al idempleado 1, si se le asigna 1 al campo tipo_usuario puede agregar, editar, o eliminar empleados. */
UPDATE empleados
SET tipo_usuario = 1
WHERE idempleado = 10;

/*Obtener información detallada sobre las columnas de una tabla*/
SHOW COLUMNS FROM mensajes_chat;

/*Agregando la columna tipo_usuario a la tabla empleados*/
ALTER TABLE empleados
ADD COLUMN tipo_usuario INT;

/*Agregando la columna tipo_puesto a la tabla empleados*/
ALTER TABLE empleados
ADD COLUMN tipo_puesto VARCHAR(60) NOT NULL;

/*Creando la tabla choferes*/
CREATE TABLE choferes (
    idempleado INT NOT NULL,
    nombre VARCHAR(60) NOT NULL,
    placas VARCHAR(60) NOT NULL,
    tipo_unidad VARCHAR(60) NOT NULL,
    PRIMARY KEY (idempleado),
    CONSTRAINT fk_empleado FOREIGN KEY (idempleado) REFERENCES empleados(idempleado)
);


/* Si es tipo_puesto = chofer, registra placas y tipo unidad en la tabla choferes */
DELIMITER $$

CREATE TRIGGER after_insert_empleado
AFTER INSERT ON empleados
FOR EACH ROW
BEGIN
    -- Verificar si el tipo de puesto es 'chofer'
    IF NEW.tipo_puesto = 'chofer' THEN
        -- Insertar el idempleado, nombre, placas y tipo_unidad en la tabla choferes
        INSERT INTO choferes (idempleado, nombre, placas, tipo_unidad)
        VALUES (NEW.idempleado, NEW.nombre, NEW.placas, NEW.tipo_unidad);
    END IF;
END$$

DELIMITER ;

INSERT INTO empleados (nombre, direccion, telefono, contrasena, email, token, confirmado, tipo_usuario, tipo_puesto, placas, tipo_unidad)
VALUES ('Juan Pérez', 'Calle Ficticia 123', '555-1234', 'password123', 'juan@email.com', NULL, 1, 2, 'chofer', 'XYZ-123', 'Camión');

/* Eliminar registro de una tabla */
DELETE FROM empleados
WHERE idempleado = 47;

/* Eliminar tablas */
Drop table unidades_de_transporte;

/* Renombrar tabla */
RENAME TABLE autobuses TO unidades_de_transporte;

/* Agregar llave foranea chofer a la tabla unidades_de_transporte donde la referencia es el idempleado */
ALTER TABLE unidades_de_transporte
ADD COLUMN chofer INT,
ADD CONSTRAINT fk_chofer
FOREIGN KEY (idempleado) REFERENCES empleados(idempleado);

ALTER TABLE unidades_de_transporte
CHANGE COLUMN idautobus idunidad INT NOT NULL AUTO_INCREMENT;

ALTER TABLE unidades_de_transporte
ADD PRIMARY KEY (idunidad);

/* Agregando la columna URL */
ALTER TABLE unidades_de_transporte
ADD COLUMN url VARCHAR(15);

ALTER TABLE unidades_de_transporte
MODIFY COLUMN url VARCHAR(32);

/* Creando tabla para guardar los mensajes del chat */
CREATE TABLE mensajes_chat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idempleado INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idempleado) REFERENCES empleados(idempleado)
);

/* Tabla manifiestos */
CREATE TABLE manifiestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(80) NOT NULL,
    mes VARCHAR(2) NOT NULL,           
    anio YEAR NOT NULL,                
    total_m3 DECIMAL(6,2) NOT NULL
);

/* Tabla clientes */
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(150) NOT NULL,
    domicilio TEXT NOT NULL,
    municipio VARCHAR(100) NOT NULL,
    estado VARCHAR(100) DEFAULT 'Jalisco',
    codigo_postal VARCHAR(10),
    correo_electronico VARCHAR(150),
    telefono VARCHAR(20)
);

/* Tabla choferes */
CREATE TABLE choferes (
  idempleado INT NOT NULL,
  nombre VARCHAR(45) NOT NULL,
  placas VARCHAR(45) NOT NULL,
  tipo_unidad VARCHAR(45) NOT NULL,
  PRIMARY KEY (idempleado)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_0900_ai_ci;
  
/* Tabla productos */
CREATE TABLE productos (
    idproducto INT NOT NULL AUTO_INCREMENT,
    descripcion VARCHAR(60) NOT NULL,
    precio DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (idproducto)
);

/* Tabla pedidos */
CREATE TABLE pedidos (
  folio INT NOT NULL AUTO_INCREMENT,
  fecha VARCHAR(50) DEFAULT NULL,
  servicio VARCHAR(45) NOT NULL,
  domicilio VARCHAR(45) NOT NULL,
  cliente VARCHAR(45) NOT NULL,
  gastos DECIMAL(15,2) DEFAULT NULL,
  costo DECIMAL(15,2) DEFAULT NULL,
  pagados DECIMAL(10,2) DEFAULT NULL,
  almacen DECIMAL(15,2) DEFAULT NULL,
  depositos DECIMAL(15,2) DEFAULT NULL,
  PRIMARY KEY (folio)
) ENGINE=InnoDB 
  AUTO_INCREMENT=32 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_0900_ai_ci;

/* Tabla unidades_de_transporte */
CREATE TABLE unidades_de_transporte (
  idunidad INT NOT NULL AUTO_INCREMENT,
  modelo VARCHAR(60) NOT NULL,
  placas VARCHAR(60) NOT NULL,
  chofer INT DEFAULT NULL,
  url VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (idunidad),
  KEY chofer (chofer)
) ENGINE=InnoDB 
  DEFAULT CHARSET=utf8mb4 
  COLLATE=utf8mb4_0900_ai_ci;

/* Historial semanal */
USE tu_base_de_datos; -- 🔄 Cambia esto al nombre de tu base de datos

CREATE TABLE historial_semanal (
  primer_folio INT NOT NULL,
  ultimo_folio INT NOT NULL,
  saldo_actual DECIMAL(15,2) NOT NULL,
  justificacion DECIMAL(15,2) NOT NULL,
  chofer INT NOT NULL,
  PRIMARY KEY (primer_folio),
  FOREIGN KEY (chofer) REFERENCES empleados(idempleado)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_0900_ai_ci;



INSERT INTO clientes (
    razon_social,
    domicilio,
    municipio,
    estado,
    codigo_postal,
    correo_electronico,
    telefono
) VALUES (
    'Comercializadora ABC S.A. de C.V.',
    'Av. Revolución 123, Col. Centro',
    'Guadalajara',
    'Jalisco',
    '44100',
    'prueba@abc.com.mx',
    '3312345678'
);

INSERT INTO unidades_de_transporte (modelo, placas, chofer, url) 
VALUES ('Toyota Hiace', 'ABC-123', 1, 'foto1.jpg');

/*Modificaciones en tabla mensajes_chat*/
/*si borras un empleado, se eliminen automáticamente todos sus mensajes asociados.*/
ALTER TABLE mensajes_chat
DROP FOREIGN KEY mensajes_chat_ibfk_1;

ALTER TABLE mensajes_chat
ADD CONSTRAINT mensajes_chat_ibfk_1
FOREIGN KEY (idempleado) REFERENCES empleados(idempleado)
ON DELETE CASCADE;

ALTER TABLE manifiestos ADD obra VARCHAR(100) AFTER cliente;
ALTER TABLE manifiestos ADD tipo_residuo VARCHAR(100) AFTER obra;

/*** Estas son las caracteristicas de cada una de las tablas que tengo para que el proyecto funcione con lo que hay en la rama victor ***/
/*choferes*/
idempleado	int	NO	PRI		
nombre	varchar(45)	NO			
placas	varchar(45)	NO			
tipo_unidad	varchar(45)	NO			

/*clientes*/
id	int	NO	PRI		auto_increment
razon_social	varchar(150)	NO			
domicilio	text	NO			
municipio	varchar(100)	NO			
estado	varchar(100)	YES		Jalisco	
codigo_postal	varchar(10)	YES			
correo_electronico	varchar(150)	YES			
telefono	varchar(20)	YES		

/*Empleados*/
idempleado	int	NO	PRI		auto_increment
nombre	varchar(60)	NO			
direccion	varchar(60)	NO			
telefono	varchar(60)	NO			
contrasena	varchar(100)	NO			
email	varchar(50)	NO			
token	varchar(255)	YES			
confirmado	tinyint(1)	YES			
tipo_usuario	int	YES			
tipo_puesto	varchar(60)	NO			

/*historial_semanal*/
primer_folio	int	NO	PRI		
ultimo_folio	int	NO			
saldo_actual	decimal(15,2)	NO			
justificacion	decimal(15,2)	NO			
chofer	int	NO	MUL		

/*manifiestos*/
id	int	NO	PRI		auto_increment
cliente	varchar(80)	NO			
obra	varchar(100)	YES			
tipo_residuo	varchar(100)	YES			
mes	varchar(2)	NO			
anio	year	NO			
total_m3	decimal(6,2)	NO			

/*mensajes_chat*/
id	int	NO	PRI		auto_increment
idempleado	int	NO	MUL		
mensaje	text	NO			
fecha	datetime	YES		CURRENT_TIMESTAMP	DEFAULT_GENERATED

/*pedidos*/
folio	int	NO	PRI		auto_increment
fecha	varchar(50)	YES			
servicio	varchar(45)	NO			
domicilio	varchar(45)	NO			
cliente	varchar(45)	NO			
gastos	decimal(15,2)	YES			
costo	decimal(15,2)	YES			
pagados	decimal(10,2)	YES			
almacen	decimal(15,2)	YES			
depositos	decimal(15,2)	YES			

/*productos*/
idproducto	int	NO	PRI		auto_increment
descripcion	varchar(60)	NO			
precio	decimal(15,2)	NO			

/*unidades_de_transporte*/
idunidad	int	NO	PRI		auto_increment
modelo	varchar(60)	NO			
placas	varchar(60)	NO			
chofer	int	YES	MUL		
url	varchar(32)	YES			
