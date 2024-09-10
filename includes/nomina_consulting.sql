/*delete DATABASE
USE master;
GO
*/

-- Asegurarse de que no hay conexiones activas a la base de datos
CREATE DATABASE NominaConsulting
USE NominaConsulting
GO



-- Creacion de tablas
CREATE TABLE Empresa (
    id_empresa INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(255) NOT NULL,
    fecha_inicio DATE NOT NULL
);

CREATE TABLE Oficina(
    id_oficina INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);

CREATE TABLE Departamento(
    id_departamento INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
);

CREATE TABLE Rol(
    id_rol INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
);


CREATE TABLE Estado(
    id_estado INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
);

CREATE TABLE Profesion(
    id_profesion INT IDENTITY(1,1) PRIMARY KEY,
	nombre NVARCHAR(50) NOT NULL,
	fk_id_empresa INT NOT NULL,
	FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
)

CREATE TABLE Empleado (
    id_empleado INT IDENTITY(1,1) PRIMARY KEY,
    nombres NVARCHAR(100) NOT NULL,
    apellidos NVARCHAR(100) NOT NULL,
    fecha_contratacion DATE NOT NULL,
    tipo_contrato NVARCHAR(50) NOT NULL,
    puesto NVARCHAR(50) NOT NULL,
    dpi_pasaporte NVARCHAR(20) UNIQUE NOT NULL,
    carnet_igss NVARCHAR(20) UNIQUE NOT NULL,
    carnet_irtra NVARCHAR(20) UNIQUE NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    numero_telefono NVARCHAR(20) UNIQUE NOT NULL,
    correo_electronico NVARCHAR(100),
    fk_id_oficina INT NOT NULL,
    fk_id_profesion INT NOT NULL,
    fk_id_departamento INT NOT NULL,
    fk_id_rol INT NOT NULL,
    fk_id_estado INT NOT NULL,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_profesion) REFERENCES Profesion(id_profesion),
    FOREIGN KEY (fk_id_oficina) REFERENCES Oficina(id_oficina),
    FOREIGN KEY (fk_id_departamento) REFERENCES Departamento(id_departamento),
    FOREIGN KEY (fk_id_rol) REFERENCES Rol(id_rol),
    FOREIGN KEY (fk_id_estado) REFERENCES Estado(id_estado),
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);

CREATE TABLE Expediente(
    id_expediente INT IDENTITY(1,1) PRIMARY KEY,
    documento NVARCHAR(255) NOT NULL,
    fk_id_empleado INT,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Usuario (
    id_usuario INT IDENTITY(1,1) PRIMARY KEY,
    correo NVARCHAR(100) NOT NULL,
    contrasena VARBINARY(64) NOT NULL,
    fk_id_empleado INT NOT NULL UNIQUE,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado),
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);

CREATE TABLE Bono14(
    id_bono INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2),
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Aguinaldo(
    id_aguinaldo INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2),
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Salario(
    id_salario INT IDENTITY(1,1) PRIMARY KEY,
    salario_base DECIMAL(8,2),
    salario_anterior DECIMAL(8,2),
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Anticipo(
    id_anticipo INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Ausencia(
    id_ausencia INT IDENTITY(1,1) PRIMARY KEY,
    motivo TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE HorasExtras(
    id_hora_extra INT IDENTITY(1,1) PRIMARY KEY,
    horas INT NOT NULL,
    tipo TEXT NOT NULL,
    fecha DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Marca(
    id_marca INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
);

CREATE TABLE Categoria(
    id_categoria INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
);

CREATE TABLE Producto(
    id_producto INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
    cantidad INT NOT NULL,
    estado TEXT NOT NULL,
    foto NVARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    fk_id_marca INT NOT NULL,
    fk_id_categoria INT NOT NULL,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_marca) REFERENCES Marca(id_marca),
    FOREIGN KEY (fk_id_categoria) REFERENCES Categoria(id_categoria),
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);

CREATE TABLE VentaTienda(
    id_venta_tienda INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto INT NOT NULL,
    monto_compra DECIMAL(8,2) NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Comisiones(
    id_comisiones INT IDENTITY(1,1) PRIMARY KEY,
    ventas INT NOT NULL,
    porcentaje FLOAT NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fecha DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Bonificacion(
    id_bonificacion INT IDENTITY(1,1) PRIMARY KEY,
    monto DECIMAL(8,2) NOT NULL,
    numero_pieza INT NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Liquidacion(
    id_liquidacion INT IDENTITY(1,1) PRIMARY KEY,
    fecha_fin_contrato DATE NOT NULL,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE Prestamos(
    id_prestamo INT IDENTITY(1,1) PRIMARY KEY,
    cuotas INT NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    saldo DECIMAL(8,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);

CREATE TABLE PolizaContable(
    id_poliza_contable INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    tipo TEXT NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);


--Llenar tablas
--Oficina
INSERT INTO Empresa(nombre, fecha_inicio) VALUES
('T Consulting, S.A.', '2024-07-17');


INSERT INTO Oficina(nombre, fk_id_empresa) VALUES 
('Recursos Humanos', 1),
('Contabilidad', 1), 
('Ventas', 1),
('Producción', 1),
('TI', 1),
('Marketing', 1),
('Servicio al Cliente', 1);

--Rol
INSERT INTO Rol(nombre) VALUES 
('Empleado'),
('Jefe Inmediato'),
('Gerente'),
('Administrador'),
('Contador');

--Departamento
INSERT INTO Departamento(nombre) VALUES
('Alta Verapaz'),
('Baja Verapaz'),
('Chimaltenango'),
('Chiquimula'),
('El Progreso'),
('Escuintla'),
('Guatemala'),
('Huehuetenango'),
('Izabal'),
('Jalapa'),
('Jutiapa'),
('Petén'),
('Quetzaltenango'),
('Quiché'),
('Retalhuleu'),
('Sacatepéquez'),
('San Marcos'),
('Santa Rosa'),
('Sololá'),
('Suchitepéquez'),
('Totonicapán'),
('Zacapa');

--Estado
INSERT INTO Estado(nombre) VALUES
('Activo'),
('Suspendido'),
('Vacaciones'),
('Incapacitado'),
('Retirado'),
('Despedido');

--Profesion
INSERT INTO Profesion(nombre, fk_id_empresa) VALUES
('Ingeniero', 1),
('Auditor', 1),
('Diseñador Gráfico', 1),
('Administrador', 1),
('Contador', 1),
('Programador', 1),
('Analista de Sistemas', 1),
('Consultor', 1),
('Gerente de Proyecto', 1),
('Técnico de Soporte', 1),
('Especialista en Recursos Humanos', 1),
('Marketing', 1),
('Ventas', 1),
('Científico de Datos', 1);

INSERT INTO Empleado(nombres, apellidos, fecha_contratacion, tipo_contrato, puesto, dpi_pasaporte, carnet_igss, carnet_irtra, fecha_nacimiento, numero_telefono, correo_electronico, fk_id_oficina, fk_id_profesion, fk_id_departamento, fk_id_rol, fk_id_estado, fk_id_empresa) 
VALUES ('Milton', 'Lopez', '2024-07-23', 'Contrato Indefinido', 'Informatica', '2955334851006', '201364483588', '2955334851006', '2002-04-28', '59541235', 'milton@gmail.com', 1, 1, 1, 1, 1, 1);


CREATE OR ALTER PROCEDURE sp_login
    @correo NVARCHAR(255),
    @contrasena NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @contrasenaBD VARBINARY(64);
    DECLARE @hashedContrasena VARBINARY(64);

    -- Obtener la contraseña almacenada en la base de datos
    SELECT @contrasenaBD = contrasena
    FROM Usuario
    WHERE correo = @correo;

    -- Verificar si la contraseña en la base de datos es NULL (usuario no encontrado)
    IF @contrasenaBD IS NULL
    BEGIN
        RAISERROR('Usuario no encontrado.', 16, 1);
        RETURN;
    END

    -- Encriptar la contraseña proporcionada para compararla
    SET @hashedContrasena = HASHBYTES('SHA2_256', @contrasena);

    -- Comparar la contraseña proporcionada (encriptada) con la almacenada
    IF @hashedContrasena = @contrasenaBD
    BEGIN
        -- Si las contraseñas coinciden, devolver los detalles del usuario
        SELECT 
            u.id_usuario AS ID,
            u.correo AS email,
            e.nombres + ' ' + e.apellidos AS username,
            em.id_empresa,
            em.nombre AS empresa
        FROM 
            Usuario u
            INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
            INNER JOIN Empresa em ON u.fk_id_empresa = em.id_empresa
        WHERE 
            u.correo = @correo;
    END
    ELSE
    BEGIN
        -- Si las contraseñas no coinciden, devolver un error
        RAISERROR('Las credenciales no coinciden.', 16, 1);
    END
END
GO


CREATE OR ALTER PROCEDURE sp_listar_usuarios
    @SearchTerm NVARCHAR(255) = NULL,
    @Offset INT = 0,
    @RowsPerPage INT = 20
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        u.id_usuario AS ID,
        u.correo AS email,
        e.nombres + ' ' + e.apellidos AS username,
        em.id_empresa,
        em.nombre AS empresa
    FROM 
        Usuario u
        INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
        INNER JOIN Empresa em ON u.fk_id_empresa = em.id_empresa
    WHERE 
        (@SearchTerm IS NULL OR
        u.correo LIKE '%' + @SearchTerm + '%' OR
        e.nombres + ' ' + e.apellidos LIKE '%' + @SearchTerm + '%' OR
        u.id_usuario LIKE '%' + @SearchTerm + '%')
    ORDER BY 
        u.id_usuario
    OFFSET @Offset ROWS 
    FETCH NEXT @RowsPerPage ROWS ONLY;
END


CREATE OR ALTER PROCEDURE sp_cambiar_contra
    @id_usuario INT,
    @nueva_contrasena NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    -- Encriptar la nueva contraseña usando SHA2_256
    DECLARE @hashedContrasena VARBINARY(64);
    SET @hashedContrasena = HASHBYTES('SHA2_256', @nueva_contrasena);

    -- Actualizar la contraseña del usuario en la tabla con el tipo de dato VARBINARY
    UPDATE Usuario
    SET contrasena = @hashedContrasena
    WHERE id_usuario = @id_usuario;
    
    -- Comprobación básica para asegurarse de que la actualización fue exitosa
    IF @@ROWCOUNT = 0
    BEGIN
        RAISERROR('No se pudo cambiar la contraseña. Usuario no encontrado.', 16, 1);
    END
END
GO


CREATE OR ALTER PROCEDURE sp_registrar_usuario
    @correo NVARCHAR(255),
    @contrasena NVARCHAR(255),
    @fk_id_empleado INT,
    @fk_id_empresa INT
AS
BEGIN
    SET NOCOUNT ON;

    -- Encriptar la contraseña usando SHA2_256
    DECLARE @hashedContrasena VARBINARY(64);
    SET @hashedContrasena = HASHBYTES('SHA2_256', @contrasena);

    -- Insertar el nuevo usuario con la contraseña encriptada
    INSERT INTO Usuario (correo, contrasena, fk_id_empleado, fk_id_empresa)
    VALUES (@correo, @hashedContrasena, @fk_id_empleado, @fk_id_empresa);

    -- Comprobación básica para asegurarse de que la inserción fue exitosa
    IF @@ROWCOUNT = 0
    BEGIN
        RAISERROR('No se pudo registrar el usuario.', 16, 1);
    END
END
GO

--Crear empleado
CREATE OR ALTER PROCEDURE sp_InsertarEmpleado
    @Nombres NVARCHAR(100),
    @Apellidos NVARCHAR(100),
    @TipoContrato NVARCHAR(50),
    @Puesto NVARCHAR(100),
    @DpiPasaporte NVARCHAR(20),
    @Salario DECIMAL(8,2),
    @CarnetIgss NVARCHAR(20),
    @CarnetIrtra NVARCHAR(20),
    @FechaNacimiento DATE,
    @CorreoElectronico NVARCHAR(100),
    @Telefono NVARCHAR(20), 
    @Expediente NVARCHAR(255),
    @Fk_Id_Oficina INT,
    @Fk_Id_Profesion INT,
    @Fk_Id_Departamento INT,
    @Fk_Id_Rol INT,
    @Fk_Id_Estado INT,
    @Fk_Id_Empresa INT
AS
BEGIN
    -- Insertar el empleado en la tabla Empleado
    INSERT INTO Empleado (nombres, apellidos, fecha_contratacion, tipo_contrato, puesto, dpi_pasaporte, carnet_igss, carnet_irtra, fecha_nacimiento, correo_electronico, 
                          numero_telefono, fk_id_oficina, fk_id_profesion, fk_id_departamento, fk_id_rol, fk_id_estado, fk_id_empresa) 
    VALUES (@Nombres, @Apellidos, GETDATE(), @TipoContrato, @Puesto, @DpiPasaporte, @CarnetIgss, @CarnetIrtra, @FechaNacimiento, @CorreoElectronico, 
            @Telefono, @Fk_Id_Oficina, @Fk_Id_Profesion, @Fk_Id_Departamento, @Fk_Id_Rol, @Fk_Id_Estado, @Fk_Id_Empresa);

    -- Obtener el ID del empleado recién insertado
    DECLARE @NuevoIdEmpleado INT;
    SET @NuevoIdEmpleado = SCOPE_IDENTITY();

    -- Insertar el salario para el nuevo empleado
    INSERT INTO Salario (salario_base, salario_anterior, fk_id_empleado) 
    VALUES (@Salario, 0, @NuevoIdEmpleado);

    -- Insertar el expediente del nuevo empleado
    INSERT INTO Expediente (documento, fk_id_empleado)
    VALUES (@Expediente, @NuevoIdEmpleado);
END
GO

-- Traer Empleados

CREATE PROCEDURE sp_listar_empleados_con_filtro
    @criterio NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        E.id_empleado, 
        E.nombres, 
        E.apellidos, 
        E.fecha_contratacion, 
        E.puesto, 
        E.dpi_pasaporte, 
        E.numero_telefono, 
        E.correo_electronico, 
        P.nombre AS profesion, 
        D.nombre AS departamento
    FROM 
        Empleado E
    INNER JOIN 
        Profesion P ON E.fk_id_profesion = P.id_profesion
    INNER JOIN 
        Departamento D ON E.fk_id_departamento = D.id_departamento
    WHERE
        E.id_empleado LIKE '%' + @criterio + '%' 
        OR E.nombres LIKE '%' + @criterio + '%' 
        OR E.apellidos LIKE '%' + @criterio + '%' 
        OR E.puesto LIKE '%' + @criterio + '%' 
        OR E.numero_telefono LIKE '%' + @criterio + '%' 
        OR E.correo_electronico LIKE '%' + @criterio + '%' 
        OR P.nombre LIKE '%' + @criterio + '%' 
        OR D.nombre LIKE '%' + @criterio + '%';
END
GO

--expediten empleado
 CREATE PROCEDURE sp_listar_expedientes_empleados
    @criterio NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        E.id_empleado, 
        E.nombres + ' ' + E.apellidos AS [Nombre completo], 
        E.numero_telefono, 
        E.correo_electronico, 
        EX.documento
    FROM 
        Empleado E
    INNER JOIN 
        Expediente EX ON E.id_empleado = EX.fk_id_empleado
    WHERE 
        E.id_empleado LIKE '%' + @criterio + '%' 
        OR E.nombres LIKE '%' + @criterio + '%' 
        OR E.apellidos LIKE '%' + @criterio + '%' 
        OR E.numero_telefono LIKE '%' + @criterio + '%' 
        OR E.correo_electronico LIKE '%' + @criterio + '%';
END
GO

