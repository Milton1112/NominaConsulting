/*
-- Asegurarse de que no hay conexiones activas a la base de datos antes de eliminarla
USE master;
GO

-- Terminar las conexiones activas a la base de datos
ALTER DATABASE NominaConsulting
SET SINGLE_USER
WITH ROLLBACK IMMEDIATE;
GO

-- Eliminar la base de datos
DROP DATABASE NominaConsulting;
GO

*/

-- Crear la base de datos
CREATE DATABASE NominaConsulting;
GO

USE NominaConsulting;
GO

-- Crear tabla Clientes
CREATE TABLE Empresa (
    id_empresa INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(255) NOT NULL UNIQUE,
    fecha_inicio DATE NOT NULL,
    numero_telefono INT UNIQUE,
    direccion_empresa TEXT,
    correo_empresa NVARCHAR(255) UNIQUE
);
GO

CREATE TABLE Oficina(
    id_oficina INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);
GO

CREATE TABLE Departamento(
    id_departamento INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
);
GO

CREATE TABLE Rol(
    id_rol INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
);
GO

CREATE TABLE Estado(
    id_estado INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(20) NOT NULL,
);
GO

CREATE TABLE Profesion(
    id_profesion INT IDENTITY(1,1) PRIMARY KEY,
	nombre NVARCHAR(50) NOT NULL,
	fk_id_empresa INT NOT NULL,
	FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);
GO

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
GO

CREATE TABLE Expediente(
    id_expediente INT IDENTITY(1,1) PRIMARY KEY,
    documento NVARCHAR(255) NOT NULL,
    fk_id_empleado INT,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Usuario (
    id_usuario INT IDENTITY(1,1) PRIMARY KEY,
    correo NVARCHAR(100) NOT NULL,
    contrasena VARBINARY(64) NOT NULL,
    fk_id_empleado INT NOT NULL UNIQUE,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado),
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa)
);
GO

CREATE TABLE Bono14(
    id_bono INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2),
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Aguinaldo(
    id_aguinaldo INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2),
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Salario(
    id_salario INT IDENTITY(1,1) PRIMARY KEY,
    salario_base DECIMAL(8,2),
    salario_anterior DECIMAL(8,2),
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Anticipo(
    id_anticipo INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Ausencia(
    id_ausencia INT IDENTITY(1,1) PRIMARY KEY,
    motivo TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE HorasExtras(
    id_hora_extra INT IDENTITY(1,1) PRIMARY KEY,
    horas INT NOT NULL,
    tipo TEXT NOT NULL,
    fecha DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Marca(
    id_marca INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
);
GO

CREATE TABLE Categoria(
    id_categoria INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
);
GO

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
GO

CREATE TABLE VentaTienda(
    id_venta_tienda INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto INT NOT NULL,
    monto_compra DECIMAL(8,2) NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Comisiones(
    id_comisiones INT IDENTITY(1,1) PRIMARY KEY,
    ventas INT NOT NULL,
    porcentaje FLOAT NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fecha DATE NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Bonificacion(
    id_bonificacion INT IDENTITY(1,1) PRIMARY KEY,
    monto DECIMAL(8,2) NOT NULL,
    numero_pieza INT NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

CREATE TABLE Liquidacion(
    id_liquidacion INT IDENTITY(1,1) PRIMARY KEY,
    fecha_fin_contrato DATE NOT NULL,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

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
GO

CREATE TABLE PolizaContable(
    id_poliza_contable INT IDENTITY(1,1) PRIMARY KEY,
    fecha DATE NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    tipo TEXT NOT NULL,
    fk_id_empleado INT NOT NULL,
    FOREIGN KEY (fk_id_empleado) REFERENCES Empleado(id_empleado)
);
GO

--Procedimientos almacenandos

--Empleados
--Crear
CREATE PROCEDURE sp_InsertarEmpleado
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
END;
GO

--Actuliazar
CREATE PROCEDURE sp_actualizar_empleado
    @id_empleado INT,
    @nombres NVARCHAR(100),
    @apellidos NVARCHAR(100),
    @tipo_contrato NVARCHAR(100),
	@puesto NVARCHAR(100),
	@dpi_pasaporte NVARCHAR(20),
	@carnet_igss NVARCHAR(20),
	@carnet_irtra NVARCHAR(20),
	@fecha_nacimiento DATE,
	@fecha_contratacion DATE,
	@correo_electronico NVARCHAR(100),
	@numero_telefono NVARCHAR(20),
	@fk_id_oficina INT,
	@fk_id_profesion INT,
    @fk_id_rol INT,
    @fk_id_estado INT
AS
BEGIN
    UPDATE Empleado
                SET nombres = @nombres, apellidos = @apellidos, 
				tipo_contrato = @tipo_contrato, 
				puesto = @puesto, dpi_pasaporte = @dpi_pasaporte, 
				carnet_igss = @carnet_igss, carnet_irtra = @carnet_irtra, 
				fecha_nacimiento =  @fecha_nacimiento,
				fecha_contratacion = @fecha_contratacion,  
				correo_electronico = @correo_electronico, numero_telefono = @numero_telefono, 
				fk_id_oficina = @fk_id_oficina, 
				fk_id_profesion = @fk_id_profesion, 
				fk_id_rol = @fk_id_rol, 
				fk_id_estado = @fk_id_estado
                WHERE id_empleado = @id_empleado
END;
GO

-- Listar
CREATE PROCEDURE sp_listar_empleados_con_filtro
    @criterio NVARCHAR(255),
    @fk_id_empresa INT 
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
    e.id_empleado,
    e.nombres,
    e.apellidos,
    e.puesto,
    e.dpi_pasaporte,
    e.numero_telefono,
    e.correo_electronico,
    e.nombres AS profesion, -- Asegúrate de incluir esta columna
    d.nombre AS departamento
    FROM 
        Empleado e
    INNER JOIN 
        Departamento d ON e.fk_id_departamento = d.id_departamento
    WHERE 
        e.fk_id_empresa = @fk_id_empresa
    AND (e.nombres LIKE '%' + @criterio + '%' OR e.apellidos LIKE '%' + @criterio + '%');
END;
GO

--Usuario
--Iniciar Sesión
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
        -- Si las contraseñas coinciden, devolver los detalles del usuario y su rol
        SELECT 
            u.id_usuario AS ID,
            u.correo AS email,
            e.nombres + ' ' + e.apellidos AS username,
            em.id_empresa,
            em.nombre AS empresa,
            r.nombre AS rol  -- Agregar rol del usuario
        FROM 
            Usuario u
            INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
            INNER JOIN Empresa em ON u.fk_id_empresa = em.id_empresa
            INNER JOIN Rol r ON e.fk_id_rol = r.id_rol  -- Unir con la tabla de roles
        WHERE 
            u.correo = @correo;
    END
    ELSE
    BEGIN
        -- Si las contraseñas no coinciden, devolver un error
        RAISERROR('Las credenciales no coinciden.', 16, 1);
    END
END;
GO

--Crear
CREATE PROCEDURE sp_registrar_usuario
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
END;
GO

--Listar
CREATE PROCEDURE sp_listar_usuarios
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
END;
GO

--Actualizar Contraeña
CREATE PROCEDURE sp_cambiar_contra
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
END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_usuario
@id INT
AS
BEGIN
    SET NOCOUNT ON;

	DELETE FROM Usuario
	WHERE id_usuario = @id;
END;
GO

--Expediente empleado
--Listar
  CREATE PROCEDURE sp_listar_expedientes_empleados
    @criterio NVARCHAR(255),
	@fk_id_empresa INT
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
	    E.fk_id_empresa = @fk_id_empresa 
	AND (E.id_empleado LIKE '%' + @criterio + '%'
    OR E.nombres LIKE '%' + @criterio + '%' 
    OR E.apellidos LIKE '%' + @criterio + '%' 
    OR E.numero_telefono LIKE '%' + @criterio + '%' 
    OR E.correo_electronico LIKE '%' + @criterio + '%');
END;
GO

--Actualizar
CREATE PROCEDURE sp_update_expediente
   @expediente NVARCHAR(255),
   @id_empleado INT
AS
BEGIN
    UPDATE Expediente
	SET documento = @expediente
	WHERE fk_id_empleado = @id_empleado
END;
GO
   
--Salario
--listar
CREATE PROCEDURE sp_listar_salario
    @criterio NVARCHAR(255),
    @fk_id_empresa INT 
AS
BEGIN
    SET NOCOUNT ON;

	SELECT 
    s.id_salario,
    e.nombres + ' ' + e.apellidos AS nombre,
    e.numero_telefono,
    s.salario_base,
    s.salario_anterior
FROM 
    Salario s
INNER JOIN 
    Empleado e ON s.fk_id_empleado = e.id_empleado
WHERE 
    e.fk_id_empresa = @fk_id_empresa
    AND (
        e.nombres LIKE '%' + @criterio + '%' 
        OR e.apellidos LIKE '%' + @criterio + '%'
        OR s.salario_base LIKE '%' + @criterio + '%'
        OR s.salario_anterior LIKE '%' + @criterio + '%'
		OR e.numero_telefono LIKE '%' + @criterio + '%'
    );

    
END;
GO

--actualizar
CREATE PROCEDURE sp_actualizar_salario
    @id INT,
    @salario decimal(8,2),
    @salarioNuevo decimal(8,2)
AS
BEGIN
    UPDATE Salario
                SET 
				    salario_anterior = @salario, salario_base = @salarioNuevo
                WHERE 
				    id_salario = @id
END;
GO

--EMPRESA
--insertar 
CREATE PROCEDURE sp_insertar_empresa
@nombre NVARCHAR(255),
@telefono INT,
@direccion TEXT,
@correo NVARCHAR(255)
AS
BEGIN

    INSERT INTO Empresa(nombre, fecha_inicio, numero_telefono, direccion_empresa, correo_empresa)
	VALUES (@nombre, GETDATE(), @telefono, @direccion, @correo);

END;
GO

--Actuliazar
CREATE PROCEDURE sp_actualizar_empresa
@id INT,
@nombre NVARCHAR(255),
@fecha DATE,
@telefono INT,
@direccion TEXT,
@correo NVARCHAR(255)
AS
BEGIN

    UPDATE 
	    Empresa
	SET
	    nombre = @nombre, fecha_inicio = @fecha, numero_telefono = @telefono, direccion_empresa = @direccion, correo_empresa = @correo
	WHERE
	    id_empresa = @id

END;
GO

--listar 
create procedure sp_listar_empresa
@criterio VARCHAR(255)
AS
BEGIN
 
    SELECT 
	    id_empresa, nombre, fecha_inicio, numero_telefono, direccion_empresa, correo_empresa
	FROM 
	    Empresa 
     WHERE 
        @criterio IS NULL OR nombre LIKE '%' + @criterio + '%';
END;
GO

--OFicinas
--Listar
CREATE PROCEDURE sp_listar_oficina
@criterio NVARCHAR(25),
@Fk_Id_Empresa INT
AS
BEGIN

	SELECT 
		o.id_oficina, o.nombre AS [Oficina] , e.nombre AS [Empresa]
	FROM 
		Oficina o
	INNER JOIN 
		Empresa e ON o.fk_id_empresa = e.id_empresa
	WHERE
        o.fk_id_empresa = @Fk_Id_Empresa
	    AND (e.nombre LIKE '%' + @criterio + '%' 
        OR o.nombre LIKE '%' + @criterio + '%');
END;
GO

--insertar
CREATE PROCEDURE sp_insertar_oficina
@nombre NVARCHAR(255),
@fk_id_empresa INT
AS
BEGIN

    INSERT INTO Oficina
	    (nombre, fk_id_empresa)
	VALUES
	    (@nombre, @fk_id_empresa);

END;
GO

--actualizar
CREATE PROCEDURE sp_actulizar_oficina
@nombre NVARCHAR(255),
@id INT
AS
BEGIN

    UPDATE 
	    Oficina
	SET
	   nombre = @nombre
	WHERE
	   id_oficina = @id;
END;
GO

--Rol
--Listar
CREATE PROCEDURE sp_listar_rol
@criterio NVARCHAR(255)
AS
BEGIN
  
    SELECT 
        id_rol, nombre
	FROM
	    Rol
	WHERE
	    nombre LIKE '%' + @criterio + '%' 

END;
GO

--crear
CREATE PROCEDURE sp_crear_rol
@rol NVARCHAR(255)
AS
BEGIN

    INSERT INTO
	    Rol(nombre)
	VALUES
	    (@rol);

END;
GO

--ACTUALIZAR
CREATE PROCEDURE sp_actualizar_rol
@nombre NVARCHAR(255),
@id INT
AS
BEGIN

    UPDATE 
	    Rol
	SET
	    nombre = @nombre
	WHERE
	    id_rol = @id;
END;
GO

--Departamento
--listar
CREATE PROCEDURE sp_listar_departamento
@criterio NVARCHAR(255)

AS
BEGIN
	select 
		id_departamento, nombre 
	from 
		Departamento
    WHERE 
        nombre LIKE '%' + @criterio + '%';
END;
GO

--Actualizar
CREATE PROCEDURE sp_actualizar_departamento
@nombre NVARCHAR(255),
@id INT
AS
BEGIN
    UPDATE
	    Departamento
	SET
	   nombre = @nombre
	WHERE
	    id_departamento = @id

END;
GO

--crear
CREATE PROCEDURE sp_insertar_departamento
@nombre NVARCHAR(255)
AS
BEGIN
    
    INSERT INTO Departamento(nombre)
    VALUES (@nombre);
END;
GO

--Estado
--Listar
CREATE PROCEDURE sp_listar_estado
@criterio NVARCHAR(255)
AS
BEGIN
	SELECT 
		id_estado, nombre
	FROM 
		Estado
	WHERE
	    nombre LIKE '%' + @criterio + '%';
END;
GO

--Crear
CREATE PROCEDURE sp_insertar_estado
@nombre NVARCHAR(255)
AS
BEGIN

    INSERT INTO Estado(nombre)
	VALUES (@nombre);

END;
GO

--Actualizar
CREATE PROCEDURE sp_actualizar_estado
@nombre NVARCHAR(255),
@id INT
AS
BEGIN

    UPDATE
	    Estado
	SET
	    nombre = @nombre

	WHERE
	    id_estado = @id

END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_estado
@id INT
AS
BEGIN

    DELETE FROM 
	    Estado
	WHERE
	    id_estado = @id;
END;
GO

--Profesion
--Listar
CREATE PROCEDURE sp_listar_profesion
@criterio NVARCHAR(255),
@IdEmpresa INT
AS
BEGIN
	select 
		p.id_profesion AS id, p.nombre, e.nombre AS Empresa 
	from 
		Profesion p
	INNER JOIN 
	    Empresa e ON p.fk_id_empresa = e.id_empresa
	WHERE
	    p.nombre LIKE '%' + @criterio + '%'
	OR
	    e.nombre LIKE '%' + @criterio + '%';;
END;
GO

--actualizar
CREATE PROCEDURE sp_actualizar_profesion
@nombre NVARCHAR(255),
@id INT
AS
BEGIN

    UPDATE
	    Profesion
	SET
	    nombre = @nombre
	WHERE
	    id_profesion = @id

END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_profesion
@id INT
AS
BEGIN

    DELETE FROM
	    Profesion
	WHERE
	    id_profesion = @id

END;
GO

--HoraExtras
--Listar
CREATE PROCEDURE sp_listar_hora_extra
    @fecha_inicio DATE = NULL,
    @fecha_fin DATE = NULL,
    @nombre NVARCHAR(255) = NULL,
    @horas INT = NULL,
    @tipo NVARCHAR(50) = NULL
AS
BEGIN
    SELECT 
        he.id_hora_extra AS id, 
        e.nombres + ' ' + e.apellidos AS Nombre,
        he.horas, 
        he.tipo, 
        he.fecha
    FROM 
        HorasExtras he
    INNER JOIN
        Empleado e ON he.fk_id_empleado = e.id_empleado
    WHERE
        (@fecha_inicio IS NULL OR he.fecha >= @fecha_inicio) AND
        (@fecha_fin IS NULL OR he.fecha <= @fecha_fin) AND
        (@nombre IS NULL OR e.nombres + ' ' + e.apellidos LIKE '%' + @nombre + '%') AND
        (@horas IS NULL OR he.horas = @horas) AND
        (@tipo IS NULL OR he.tipo LIKE '%' + @tipo + '%')
END;
GO

--Insertar
CREATE PROCEDURE sp_insertar_horas_extras
@horas INT,
@tipo TEXT,
@fecha DATE,
@IdEmpleado INT
AS
BEGIN

    INSERT INTO
	    HorasExtras(horas, tipo, fecha, fk_id_empleado)
	VALUES
	    (@horas, @tipo, @fecha, @IdEmpleado);

END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_horas_extras
@id INT
AS
BEGIN

    DELETE FROM HorasExtras
	WHERE
	    id_hora_extra = @id;

END;
GO

-- INSERTAR DATOS
--EMPRESA
EXEC sp_insertar_empresa
@nombre = 'T Consulting, S.A.',
@telefono = '58131409',
@direccion = 'Av. 9-00 Z.2',
@correo = 'nomina-consulting@guatemala.com';
GO

--Oficina
INSERT INTO Oficina(nombre, fk_id_empresa) VALUES 
('Recursos Humanos', 1),
('Contabilidad', 1), 
('Ventas', 1),
('Producción', 1),
('TI', 1),
('Marketing', 1),
('Servicio al Cliente', 1);
GO

--Rol
INSERT INTO Rol(nombre) VALUES 
('Empleado'),
('Jefe Inmediato'),
('Gerente'),
('Administrador'),
('Contador');
GO

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
GO

--Estado
INSERT INTO Estado(nombre) VALUES
('Activo'),
('Suspendido'),
('Vacaciones'),
('Incapacitado'),
('Retirado'),
('Despedido');
GO

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
GO

--INSERTAR ADMINISTRADOR
EXEC sp_InsertarEmpleado 
    @Nombres = 'Admin',
    @Apellidos = 'Admin',
    @TipoContrato = 'Contrato Indefinido',
    @Puesto = 'Informatica',
    @DpiPasaporte = '2955334851006',
    @Salario = 0.00,  -- Salario Base
    @CarnetIgss = '201364483588',
    @CarnetIrtra = '2955334851006',
    @FechaNacimiento = '2002-04-28',
    @CorreoElectronico = 'admin@consulting-nomina.com',
    @Telefono = '59541235',
    @Expediente = 'NULL',  -- Documento en Expediente como NULL
    @Fk_Id_Oficina = 1,
    @Fk_Id_Profesion = 1,
    @Fk_Id_Departamento = 1,
    @Fk_Id_Rol = 4,
    @Fk_Id_Estado = 1,
    @Fk_Id_Empresa = 1;
GO

-- INSERTAR USUARIO
EXEC sp_registrar_usuario 
    @correo = 'admin@consulting-nomina.com',
    @contrasena = 'Holaque123',
    @fk_id_empleado = 1,
    @fk_id_empresa = 1;
GO
