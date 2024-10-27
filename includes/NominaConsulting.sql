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
    fecha_solicitud DATE NOT NULL,
    monto_solicitado DECIMAL(8,2) NOT NULL,
    estado NVARCHAR(10) CHECK( estado IN ('Pendiente', 'Aprobado', 'Rechazado')) NOT NULL,
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
    precio DECIMAL(8,2) NOT NULL,
    estado TEXT NOT NULL,
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
    monto_total INT NOT NULL,
    cantidad INT NOT NULL,
    fk_id_empleado INT NOT NULL,
    fk_id_Producto INT NOT NULL,
    fk_id_empresa INT NOT NULL,
    FOREIGN KEY (fk_id_Producto) REFERENCES Producto(id_producto),
    FOREIGN KEY (fk_id_empresa) REFERENCES Empresa(id_empresa),
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

--CREAR 
CREATE PROCEDURE sp_insertar_profesion
@nombre NVARCHAR(255),
@idEmpresa INT
AS
BEGIN

    INSERT INTO 
	    Profesion(nombre, fk_id_empresa)
	VALUES
	    (@nombre, @idEmpresa);

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

--actualizar
CREATE PROCEDURE sp_actualizar_horas_extras
@id INT,
@horas INT,
@tipo TEXT,
@fecha DATE
AS
BEGIN

    UPDATE
	    HorasExtras
	SET
	    horas = @horas,
		tipo = @tipo,
		fecha = @fecha
	WHERE
	    id_hora_extra = @id;

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

--Anticipo
--listar
CREATE PROCEDURE sp_listar_anticipo
    @criterio NVARCHAR(255), 
    @fecha DATE               
AS
BEGIN
    SELECT 
        a.id_anticipo AS id, 
        e.nombres + ' ' + e.apellidos AS Nombre,
        a.fecha_solicitud, 
        a.monto_solicitado, 
        a.estado
    FROM 
        Anticipo a
    INNER JOIN 
        Empleado e ON a.fk_id_empleado = e.id_empleado
    WHERE 
        (e.nombres LIKE '%' + @criterio + '%' OR
         e.apellidos LIKE '%' + @criterio + '%' OR
         a.monto_solicitado LIKE '%' + @criterio + '%' OR
         a.estado LIKE '%' + @criterio + '%')
        AND (@fecha IS NULL OR a.fecha_solicitud = @fecha)
END;
GO

--Insertar
CREATE PROCEDURE sp_insertar_anticipo
    @id INT,              
    @fecha DATE,          
    @estado NVARCHAR(50)  
AS
BEGIN
    -- Declaración de variables
    DECLARE @salario_base DECIMAL(10, 2);
    DECLARE @monto DECIMAL(10, 2);
    DECLARE @existe_anticipo INT;

    -- Verificar si ya existe un anticipo en el mismo mes para el empleado
    SELECT @existe_anticipo = COUNT(*)
    FROM Anticipo
    WHERE fk_id_empleado = @id
      AND MONTH(fecha_solicitud) = MONTH(@fecha)
      AND YEAR(fecha_solicitud) = YEAR(@fecha);

    -- Si existe un anticipo, no permitir la inserción
    IF @existe_anticipo > 0
    BEGIN
        RAISERROR('El empleado ya tiene un anticipo registrado en este mes.', 16, 1);
        RETURN;
    END

    -- Obtener el salario base del empleado
    SELECT @salario_base = salario_base
    FROM Salario
    WHERE fk_id_empleado = @id;

    -- Calcular el 30% del salario base
    SET @monto = @salario_base * 0.30;

    -- Insertar los valores en la tabla Anticipo
    INSERT INTO Anticipo(fecha_solicitud, monto_solicitado, estado, fk_id_empleado)
    VALUES(@fecha, @monto, @estado, @id);
END;
GO

--actualizar
CREATE PROCEDURE sp_actualizar_anticipo
@id INT,
@estado NVARCHAR(50)
AS
BEGIN
    
    UPDATE 
        Anticipo
    SET
        estado = @estado
    WHERE
        id_anticipo = @id;
END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_anticipo
@id INT
AS
BEGIN

    DELETE FROM Anticipo
	WHERE
	    id_anticipo = @id;

END;
GO

--Ausencia
--listar
CREATE PROCEDURE sp_listar_ausencia
    @criterio NVARCHAR(255), 
    @fechaInicio DATE,       
    @fechaFin DATE,          
    @idEmpresa INT           
AS
BEGIN
    SELECT
        id_ausencia, 
        e.nombres + ' ' + e.apellidos AS Nombre,
        a.fecha_inicio, 
        a.fecha_fin, 
        a.motivo
    FROM 
        Ausencia a
    INNER JOIN 
        Empleado e ON a.fk_id_empleado = e.id_empleado
    WHERE
        e.fk_id_empresa = @idEmpresa
        AND (
            (@fechaInicio IS NULL OR a.fecha_inicio >= @fechaInicio)
            AND (@fechaFin IS NULL OR a.fecha_fin <= @fechaFin)
        )
        AND (
            e.nombres LIKE '%' + @criterio + '%' 
            OR e.apellidos LIKE '%' + @criterio + '%' 
            OR a.motivo LIKE '%' + @criterio + '%' 
        )
END;
GO

--Insertar
CREATE PROCEDURE sp_insertar_ausencia
    @fechaInicio DATE,      
    @fechaFin DATE,         
    @motivo NVARCHAR(255),  
    @IdEmpleado INT,
    @estado NVARCHAR(50)
AS
BEGIN
    -- Declaración de variables
    DECLARE @numPermisos INT;
    DECLARE @IdEstado INT;

    -- Contar la cantidad de permisos ("Permiso") solicitados en el mes para este empleado
    SELECT @numPermisos = COUNT(*)
    FROM Ausencia
    WHERE fk_id_empleado = @IdEmpleado
      AND CONVERT(VARCHAR(MAX), motivo) = 'Permiso'  
      AND MONTH(fecha_inicio) = MONTH(@fechaInicio)
      AND YEAR(fecha_inicio) = YEAR(@fechaInicio);

    -- Verificar si ya tiene 3 permisos en el mismo mes
    IF @numPermisos >= 3
    BEGIN
        RAISERROR('El empleado ha alcanzado el límite de 3 permisos en este mes según la política de la empresa.', 16, 1);
        RETURN;
    END

    -- Insertar los valores en la tabla Ausencia si no se ha excedido el límite
    INSERT INTO Ausencia(fecha_inicio, fecha_fin, motivo, fk_id_empleado)
    VALUES (@fechaInicio, @fechaFin, @motivo, @IdEmpleado);

    -- Verificar si el estado proporcionado existe
    SELECT @IdEstado = id_estado
    FROM Estado
    WHERE nombre = @estado;

    -- Imprimir para depuración
    PRINT 'ID de estado encontrado: ' + ISNULL(CAST(@IdEstado AS NVARCHAR(10)), 'NULL');

    -- Si no existe el estado, lanzar un mensaje de advertencia
    IF @IdEstado IS NULL
    BEGIN
        PRINT 'No se encontró el estado con el nombre: ' + @estado;
        RETURN;
    END

    -- Actualizar el estado del empleado
    UPDATE Empleado
    SET fk_id_estado = @IdEstado
    WHERE id_empleado = @IdEmpleado;

    -- Confirmar la actualización
    PRINT 'Empleado actualizado con ID de estado: ' + CAST(@IdEstado AS NVARCHAR(10));
END;
GO

--ELIMINAR
CREATE PROCEDURE sp_eliminar_ausencia
@id INT
AS
BEGIN

    DELETE FROM Ausencia
	WHERE 
	    id_ausencia = @id;

END;
GO

--Marca
--Listar
CREATE PROCEDURE sp_listar_marca
@criterio NVARCHAR(255)
AS
BEGIN
	SELECT
		id_marca, nombre
	FROM 
		Marca
	WHERE
	    nombre LIKE '%' + @criterio + '%';
END;
GO

--Agregar
CREATE PROCEDURE sp_insertar_marca
@nombre NVARCHAR(255)
AS
BEGIN

    INSERT INTO 
        Marca(nombre)
    VALUES
        (@nombre);

END;
GO

--Actualizar
CREATE PROCEDURE sp_actualizar_marca
@id INT,
@nombre NVARCHAR(255)
AS
BEGIN
    
    UPDATE
	    Marca
	SET
	    nombre = @nombre
	WHERE
	    id_marca = @id

END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_marca
@id INT
AS
BEGIN
    
    DELETE FROM 
	    Marca
	WHERE
	    id_marca = @id

END;
GO

--Categoria
--Listar
CREATE PROCEDURE sp_listar_categoria
@criterio NVARCHAR(255)
AS
BEGIN
	SELECT
		id_categoria, nombre
	FROM 
		Categoria
	WHERE
	    nombre LIKE '%' + @criterio + '%';
END;
GO

--Agregar
CREATE PROCEDURE sp_insertar_categoria
@nombre NVARCHAR(255)
AS
BEGIN

    INSERT INTO 
        Categoria(nombre)
    VALUES
        (@nombre);

END;
GO

--Actualizar
CREATE PROCEDURE sp_actualizar_categoria
@id INT,
@nombre NVARCHAR(255)
AS
BEGIN
    
    UPDATE
	    Categoria
	SET
	    nombre = @nombre
	WHERE
	    id_categoria = @id

END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_categoria
@id INT
AS
BEGIN
    
    DELETE FROM 
	    Categoria
	WHERE
	    id_categoria = @id

END;
GO

--Bonificación


--Bono14
--Listar
CREATE PROCEDURE sp_listar_bono14
    @idEmpresa INT,
    @criterio NVARCHAR(255),
    @fecha_inicio DATE = NULL,  
    @fecha_fin DATE = NULL      
AS
BEGIN
    SELECT
        b.id_bono, 
        e.nombres + ' ' + e.apellidos AS Nombre, 
        b.monto, 
        b.fecha, 
        em.nombre AS Empresa
    FROM
        Bono14 b
    INNER JOIN 
        Empleado e ON b.fk_id_empleado = e.id_empleado
    INNER JOIN
        Empresa em ON e.fk_id_empresa = em.id_empresa
    WHERE
        e.fk_id_empresa = @idEmpresa
        AND (e.nombres LIKE '%' + @criterio + '%' 
             OR e.apellidos LIKE '%' + @criterio + '%')
        AND (b.fecha BETWEEN @fecha_inicio AND @fecha_fin 
             OR @fecha_inicio IS NULL 
             OR @fecha_fin IS NULL);
END;
GO

--Crear
CREATE PROCEDURE generar_bono14
    @id_empresa INT, -- Empresa solicitada
    @anio INT       -- Año para generar el bono
AS
BEGIN
    -- Declarar la fecha del 15 de julio del año en curso
    DECLARE @fecha_julio DATE = CAST(CAST(@anio AS VARCHAR(4)) + '-07-15' AS DATE);
    
    -- Insertar el Bono 14, asegurándose de que no se inserte más de un bono por año por empleado
    INSERT INTO Bono14 (fk_id_empleado, fecha, monto)
    SELECT 
        e.id_empleado,
        @fecha_julio AS fecha,
        -- Calcular el bono correctamente utilizando la fórmula ajustada para contar 22 días laborales por mes y limitar a 264 días
        CASE 
            -- Si el empleado fue contratado antes del 15 de julio del año en curso
            WHEN e.fecha_contratacion <= @fecha_julio 
            THEN 
                -- Calcular el bono considerando 22 días por mes y limitando a un máximo de 264 días
                (s.salario_base / 365) * 
                CASE 
                    -- Obtener los días trabajados (22 días por mes desde la contratación hasta el 15 de julio)
                    WHEN DATEDIFF(MONTH, e.fecha_contratacion, @fecha_julio) * 22 <= 264 
                    THEN DATEDIFF(MONTH, e.fecha_contratacion, @fecha_julio) * 22
                    ELSE 264
                END
            -- Si el empleado fue contratado después del 15 de julio del año anterior, calcular para el año siguiente
            ELSE 
                -- Calcular el bono proporcional para el año siguiente desde el 1 de enero hasta el 15 de julio
                -- También limitando el cálculo a un máximo de 264 días trabajados
                (s.salario_base / 365) * 
                CASE 
                    WHEN DATEDIFF(MONTH, CAST(CAST(@anio AS VARCHAR(4)) + '-01-01' AS DATE), @fecha_julio) * 22 <= 264
                    THEN DATEDIFF(MONTH, CAST(CAST(@anio AS VARCHAR(4)) + '-01-01' AS DATE), @fecha_julio) * 22
                    ELSE 264
                END
        END AS monto
    FROM 
        Empleado e
    INNER JOIN 
        Salario s ON e.id_empleado = s.fk_id_empleado
    INNER JOIN 
        Empresa em ON e.fk_id_empresa = em.id_empresa
    WHERE 
        e.fk_id_empresa = @id_empresa
        -- Solo empleados contratados antes del 15 de julio del año en curso o que se calculen para los años siguientes
        AND (e.fecha_contratacion <= @fecha_julio 
             OR (YEAR(e.fecha_contratacion) < @anio AND e.fecha_contratacion > CAST(CAST(@anio - 1 AS VARCHAR(4)) + '-07-15' AS DATE)))
        AND NOT EXISTS (
            -- Asegurar que no haya un Bono 14 ya registrado para este empleado en el mismo año
            SELECT 1 
            FROM Bono14 b 
            WHERE b.fk_id_empleado = e.id_empleado 
            AND YEAR(b.fecha) = @anio
        );
END;
GO

--Eliminar Bono14:
CREATE PROCEDURE sp_eliminar_bono14
    @id_bono INT,               -- ID del bono específico
    @eliminar_todos BIT = 0     -- Indica si se eliminan todos los bonos del empleado (1 = Sí, 0 = No)
AS
BEGIN
    SET NOCOUNT ON;

    IF @eliminar_todos = 1
    BEGIN
        -- Eliminar todos los bonos del empleado relacionado con @id_bono
        DELETE FROM Bono14
        WHERE fk_id_empleado = (SELECT fk_id_empleado FROM Bono14 WHERE id_bono = @id_bono);

        SELECT 'Todos los bonos del empleado fueron eliminados.' AS Resultado;
    END
    ELSE
    BEGIN
        -- Eliminar solo el bono específico
        DELETE FROM Bono14
        WHERE id_bono = @id_bono;

        SELECT 'El bono específico fue eliminado.' AS Resultado;
    END
END;
GO


--Planilla
CREATE VIEW planilla_empleado AS
SELECT 
     e.id_empleado, 
     e.nombres + ' ' + e.apellidos AS Nombre,
     e.puesto + ' ' + o.nombre AS Cargo, 
     o.nombre AS Dependencia,
     s.salario_base, 
     CAST(s.salario_base * 0.0483 AS DECIMAL(8,2)) AS Descuento_IGSS,
     CAST(COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Horas_Extras,  
     CAST(s.salario_base - (s.salario_base * 0.0483) + COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Liquido
FROM 
    Empleado e
INNER JOIN
    Salario s ON s.fk_id_empleado = e.id_empleado
INNER JOIN 
    Oficina o ON e.fk_id_oficina = o.id_oficina
LEFT JOIN 
    (SELECT 
         fk_id_empleado, 
         SUM(horas) AS horas  
     FROM 
         HorasExtras
     WHERE 
         MONTH(fecha) = MONTH(GETDATE())  
         AND YEAR(fecha) = YEAR(GETDATE())
     GROUP BY 
         fk_id_empleado) he ON he.fk_id_empleado = e.id_empleado;
GO

--Listar
CREATE PROCEDURE sp_listar_planilla
    @criterio NVARCHAR(255),  -- Criterio de búsqueda general
    @idEmpresa INT,           -- ID de la empresa
    @fecha_inicio DATE = NULL, -- Fecha de inicio del rango (opcional)
    @fecha_fin DATE = NULL     -- Fecha de fin del rango (opcional)
AS
BEGIN
    SELECT 
        e.id_empleado, 
        e.nombres + ' ' + e.apellidos AS Nombre,
        e.puesto + ' ' + o.nombre AS Cargo, 
        o.nombre AS Dependencia,
        s.salario_base, 
        CAST(s.salario_base * 0.0483 AS DECIMAL(8,2)) AS Descuento_IGSS,
        CAST(COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Horas_Extras,  
        CAST(s.salario_base - (s.salario_base * 0.0483) + COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Liquido,
        e.fecha_contratacion  -- Incluimos este campo para evitar errores en el PHP
    FROM 
        Empleado e
    INNER JOIN
        Salario s ON s.fk_id_empleado = e.id_empleado
    INNER JOIN 
        Oficina o ON e.fk_id_oficina = o.id_oficina
    LEFT JOIN 
        (SELECT 
             fk_id_empleado, 
             SUM(horas) AS horas  
         FROM 
             HorasExtras
         WHERE 
             MONTH(fecha) = MONTH(GETDATE())  
             AND YEAR(fecha) = YEAR(GETDATE())
         GROUP BY 
             fk_id_empleado) he ON he.fk_id_empleado = e.id_empleado
    WHERE
        e.fk_id_empresa = @idEmpresa
        AND (e.fecha_contratacion BETWEEN @fecha_inicio AND @fecha_fin OR @fecha_inicio IS NULL OR @fecha_fin IS NULL)
        AND (
            e.nombres + ' ' + e.apellidos LIKE '%' + @criterio + '%' 
            OR o.nombre LIKE '%' + @criterio + '%'
            OR e.puesto LIKE '%' + @criterio + '%'
            OR CAST(s.salario_base AS NVARCHAR(255)) LIKE '%' + @criterio + '%'
        );
END;
GO

--Quincena1
CREATE PROCEDURE sp_quincena1
    @criterio NVARCHAR(255),  -- Criterio de búsqueda general
    @idEmpresa INT            -- ID de la empresa
AS
BEGIN
    DECLARE @fecha_inicio DATE = DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 1);
    DECLARE @fecha_fin DATE = DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 15);

    SELECT 
        e.id_empleado, 
        e.nombres + ' ' + e.apellidos AS Nombre,
        e.puesto + ' ' + o.nombre AS Cargo, 
        o.nombre AS Dependencia,
        s.salario_base, 
        CAST(s.salario_base * 0.0483 AS DECIMAL(8,2)) AS Descuento_IGSS,
        CAST(COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Horas_Extras,  
        CAST(s.salario_base / 2 AS DECIMAL(8,2)) AS Salario_Quincena, -- Salario para la quincena
        CAST((s.salario_base / 2) - (s.salario_base * 0.0483) + COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Liquido_Quincenal,
        e.fecha_contratacion
    FROM 
        Empleado e
    INNER JOIN
        Salario s ON s.fk_id_empleado = e.id_empleado
    INNER JOIN 
        Oficina o ON e.fk_id_oficina = o.id_oficina
    LEFT JOIN 
        (SELECT 
             fk_id_empleado, 
             SUM(horas) AS horas  
         FROM 
             HorasExtras
         WHERE 
             fecha BETWEEN @fecha_inicio AND @fecha_fin
         GROUP BY 
             fk_id_empleado) he ON he.fk_id_empleado = e.id_empleado
    WHERE
        e.fk_id_empresa = @idEmpresa
        AND (
            e.nombres + ' ' + e.apellidos LIKE '%' + @criterio + '%' 
            OR o.nombre LIKE '%' + @criterio + '%'
            OR e.puesto LIKE '%' + @criterio + '%'
            OR CAST(s.salario_base AS NVARCHAR(255)) LIKE '%' + @criterio + '%'
        );
END;
GO

--Quincena2
CREATE PROCEDURE sp_quincena2
    @criterio NVARCHAR(255),  -- Criterio de búsqueda general
    @idEmpresa INT            -- ID de la empresa
AS
BEGIN
    DECLARE @fecha_inicio DATE = DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 16);
    DECLARE @fecha_fin DATE = EOMONTH(GETDATE());  -- Último día del mes actual

    SELECT 
        e.id_empleado, 
        e.nombres + ' ' + e.apellidos AS Nombre,
        e.puesto + ' ' + o.nombre AS Cargo, 
        o.nombre AS Dependencia,
        s.salario_base, 
        CAST(s.salario_base * 0.0483 AS DECIMAL(8,2)) AS Descuento_IGSS,
        CAST(COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Horas_Extras,  
        CAST(s.salario_base / 2 AS DECIMAL(8,2)) AS Salario_Quincena, -- Salario para la quincena
        CAST((s.salario_base / 2) - (s.salario_base * 0.0483) + COALESCE((s.salario_base / 22 / 8) * he.horas, 0) AS DECIMAL(8,2)) AS Liquido_Quincenal,
        e.fecha_contratacion
    FROM 
        Empleado e
    INNER JOIN
        Salario s ON s.fk_id_empleado = e.id_empleado
    INNER JOIN 
        Oficina o ON e.fk_id_oficina = o.id_oficina
    LEFT JOIN 
        (SELECT 
             fk_id_empleado, 
             SUM(horas) AS horas  
         FROM 
             HorasExtras
         WHERE 
             fecha BETWEEN @fecha_inicio AND @fecha_fin
         GROUP BY 
             fk_id_empleado) he ON he.fk_id_empleado = e.id_empleado
    WHERE
        e.fk_id_empresa = @idEmpresa
        AND (
            e.nombres + ' ' + e.apellidos LIKE '%' + @criterio + '%' 
            OR o.nombre LIKE '%' + @criterio + '%'
            OR e.puesto LIKE '%' + @criterio + '%'
            OR CAST(s.salario_base AS NVARCHAR(255)) LIKE '%' + @criterio + '%'
        );
END;
GO


--Producto
--LISTAR
CREATE PROCEDURE sp_listar_producto
    @criterio NVARCHAR(255),
    @idEmpresa INT
AS
BEGIN
    SELECT
        p.id_producto, p.nombre, p.cantidad, p.precio, p.estado, p.descripcion,
        m.nombre AS Marca, c.nombre AS Categoria,
        e.nombre AS Nombre
    FROM
        Producto p
    INNER JOIN
        Marca m ON p.fk_id_marca = m.id_marca
    INNER JOIN
        Categoria c ON p.fk_id_categoria = c.id_categoria
    INNER JOIN
        Empresa e ON p.fk_id_empresa = e.id_empresa
    WHERE
        fk_id_empresa = @idEmpresa
        AND (e.nombre LIKE '%' + @criterio + '%' 
             OR p.nombre LIKE '%' + @criterio + '%'
             OR p.estado LIKE '%' + @criterio + '%'
             OR p.descripcion LIKE '%' + @criterio + '%'
             OR m.nombre LIKE '%' + @criterio + '%'
             OR c.nombre LIKE '%' + @criterio + '%');
END;
GO

--Crear
CREATE PROCEDURE sp_insertar_producto
    @nombre NVARCHAR(255),
    @cantidad INT,
    @precio DECIMAL(8,2),
    @estado NVARCHAR(255),
    @descripcion NVARCHAR(255),
    @idMarca INT,
    @idCategoria INT,
    @idEmpresa INT
AS
BEGIN
    INSERT INTO
        Producto(nombre, cantidad, precio, estado, descripcion, 
                 fk_id_marca, fk_id_categoria, fk_id_empresa)
    VALUES
        (@nombre, @cantidad, @precio, @estado, @descripcion,
         @idMarca, @idCategoria, @idEmpresa);
END;
GO

--actualizar
CREATE PROCEDURE sp_actualizar_producto
    @idProducto INT,
    @nombre NVARCHAR(255),
    @cantidad INT,
    @precio DECIMAL(8,2),
    @estado NVARCHAR(255),
    @descripcion NVARCHAR(255),
    @idMarca INT,
    @idCategoria INT,
    @idEmpresa INT
AS
BEGIN
    UPDATE Producto
    SET nombre = @nombre,
        cantidad = @cantidad,
        precio = @precio,
        estado = @estado,
        descripcion = @descripcion,
        fk_id_marca = @idMarca,
        fk_id_categoria = @idCategoria,
        fk_id_empresa = @idEmpresa
    WHERE id_producto = @idProducto;
END;
GO

--Venta
--Listar
CREATE PROCEDURE sp_listar_venta
    @criterio NVARCHAR(255),
    @idEmpresa INT,
    @fechaInicio DATE = NULL,
    @fechaFin DATE = NULL
AS
BEGIN
    SELECT
        vt.id_venta_tienda, 
        p.nombre AS [Nombre Producto],
        p.precio AS [Precio],
        c.nombre AS [Categoria],
        m.nombre AS [Marca],
        vt.cantidad AS [Cantidad Vendida],
        vt.fecha, 
        vt.monto_total AS [Monto Total],
        e.nombres + ' ' + e.apellidos AS [Empleado],
        em.nombre AS [Empresa]
    FROM
        VentaTienda vt
    INNER JOIN
        Producto p ON vt.fk_id_Producto = p.id_producto 
    INNER JOIN
        Empresa em ON vt.fk_id_empresa = em.id_empresa
    INNER JOIN
        Empleado e ON vt.fk_id_empleado = e.id_empleado
    INNER JOIN
        Categoria c ON p.fk_id_categoria = c.id_categoria
    INNER JOIN
        Marca m ON p.fk_id_marca = m.id_marca
    WHERE
        em.id_empresa = @idEmpresa
        AND (e.nombres LIKE '%' + @criterio + '%' 
             OR p.nombre LIKE '%' + @criterio + '%'
             OR CAST(p.precio AS NVARCHAR(255)) LIKE '%' + @criterio + '%'
             OR c.nombre LIKE '%' + @criterio + '%'
             OR m.nombre LIKE '%' + @criterio + '%'
             OR CAST(vt.fecha AS NVARCHAR(255)) LIKE '%' + @criterio + '%'
             OR em.nombre LIKE '%' + @criterio + '%')
        AND (vt.fecha >= @fechaInicio OR @fechaInicio IS NULL)
        AND (vt.fecha <= @fechaFin OR @fechaFin IS NULL);
END;
GO


--Agregar Venta
CREATE PROCEDURE sp_venta_tienda
    @fecha DATE,
    @cantidad INT,
    @dpi_pasaporte NVARCHAR(20),
    @id_producto INT,
    @id_empresa INT
AS
BEGIN
    -- Declaración de variables para almacenar los IDs, cantidad y precio
    DECLARE @id_empleado INT;
    DECLARE @cantidad_disponible INT;
    DECLARE @precio DECIMAL(8,2);
    DECLARE @monto_total DECIMAL(10,2);

    -- Verificar si el empleado existe y pertenece a la empresa especificada, y obtener su ID
    SELECT @id_empleado = id_empleado
    FROM Empleado
    WHERE dpi_pasaporte = @dpi_pasaporte AND fk_id_empresa = @id_empresa;

    -- Si el empleado no existe o no pertenece a la empresa, mostrar mensaje y salir
    IF @id_empleado IS NULL
    BEGIN
        PRINT 'El empleado no existe o no pertenece a la empresa especificada';
        RETURN;
    END

    -- Verificar si el producto existe y pertenece a la empresa especificada, y obtener su cantidad disponible y precio
    SELECT @cantidad_disponible = cantidad, @precio = precio
    FROM Producto
    WHERE id_producto = @id_producto AND fk_id_empresa = @id_empresa;

    -- Si el producto no existe o no pertenece a la empresa, mostrar mensaje y salir
    IF @cantidad_disponible IS NULL
    BEGIN
        PRINT 'El producto no existe o no pertenece a la empresa especificada';
        RETURN;
    END

    -- Verificar que haya suficiente cantidad del producto disponible
    IF @cantidad_disponible < @cantidad
    BEGIN
        PRINT 'No hay suficiente cantidad disponible del producto';
        RETURN;
    END

    -- Calcular el monto total basado en la cantidad y el precio del producto
    SET @monto_total = @cantidad * @precio;

    -- Insertar la venta en la tabla VentaTienda
    INSERT INTO VentaTienda (fecha, monto_total, cantidad, fk_id_empleado, fk_id_Producto, fk_id_empresa)
    VALUES (@fecha, @monto_total, @cantidad, @id_empleado, @id_producto, @id_empresa);

    -- Actualizar la cantidad disponible en la tabla Producto
    UPDATE Producto
    SET cantidad = cantidad - @cantidad
    WHERE id_producto = @id_producto AND fk_id_empresa = @id_empresa;

    PRINT 'Venta registrada exitosamente y cantidad actualizada';
END;
GO

--Eliminar Venta
CREATE PROCEDURE sp_eliminar_venta
    @idVentaTienda INT,
    @idEmpresa INT
AS
BEGIN
    -- Declarar variables para almacenar la cantidad y el ID del producto
    DECLARE @cantidad INT;
    DECLARE @idProducto INT;

    -- Obtener la cantidad vendida y el ID del producto de la venta
    SELECT @cantidad = cantidad, @idProducto = fk_id_Producto
    FROM VentaTienda
    WHERE id_venta_tienda = @idVentaTienda AND fk_id_empresa = @idEmpresa;

    -- Verificar si la venta existe
    IF @cantidad IS NULL OR @idProducto IS NULL
    BEGIN
        PRINT 'La venta especificada no existe o no pertenece a la empresa indicada';
        RETURN;
    END

    -- Actualizar la cantidad del producto, sumando la cantidad de la venta
    UPDATE Producto
    SET cantidad = cantidad + @cantidad
    WHERE id_producto = @idProducto AND fk_id_empresa = @idEmpresa;

    -- Eliminar la venta de la tabla VentaTienda
    DELETE FROM VentaTienda
    WHERE id_venta_tienda = @idVentaTienda AND fk_id_empresa = @idEmpresa;

    PRINT 'Venta eliminada exitosamente y cantidad del producto actualizada';
END;
GO

--Aguinaldo
--Listar
CREATE PROCEDURE sp_listar_aguinaldo
    @fk_id_empresa INT,
    @criterio NVARCHAR(255),
    @fecha_inicio DATE = NULL,
    @fecha_fin DATE = NULL
AS
BEGIN
    SELECT 
        a.id_aguinaldo,
        e.nombres + ' ' + e.apellidos AS Nombre,
        a.monto,
        a.fecha,
        em.nombre AS Empresa
    FROM 
        Aguinaldo a
    INNER JOIN 
        Empleado e ON a.fk_id_empleado = e.id_empleado
    INNER JOIN 
        Empresa em ON e.fk_id_empresa = em.id_empresa
    WHERE 
        e.fk_id_empresa = @fk_id_empresa
        AND (e.nombres + ' ' + e.apellidos LIKE '%' + @criterio + '%' OR @criterio IS NULL)
        AND (a.fecha BETWEEN @fecha_inicio AND @fecha_fin OR @fecha_inicio IS NULL OR @fecha_fin IS NULL);
END;
GO

--Crear
CREATE PROCEDURE generar_aguinaldo
    @id_empresa INT, -- Empresa solicitada
    @anio INT       -- Año para generar el aguinaldo
AS
BEGIN
    -- Declarar la fecha del 20 de diciembre del año en curso
    DECLARE @fecha_diciembre DATE = CAST(CAST(@anio AS VARCHAR(4)) + '-12-20' AS DATE);
    
    -- Insertar el aguinaldo, asegurándose de que no se inserte más de un aguinaldo por año por empleado
    INSERT INTO Aguinaldo (fk_id_empleado, fecha, monto)
    SELECT 
        e.id_empleado,
        @fecha_diciembre AS fecha,
        -- Calcular el aguinaldo correctamente utilizando la fórmula ajustada para contar 22 días laborales por mes y limitar a 264 días
        CASE 
            -- Si el empleado fue contratado antes del 20 de diciembre del año en curso
            WHEN e.fecha_contratacion <= @fecha_diciembre 
            THEN 
                -- Calcular el aguinaldo considerando 22 días por mes y limitando a un máximo de 264 días
                (s.salario_base / 365) * 
                CASE 
                    -- Obtener los días trabajados (22 días por mes desde la contratación hasta el 20 de diciembre)
                    WHEN DATEDIFF(MONTH, e.fecha_contratacion, @fecha_diciembre) * 22 <= 264 
                    THEN DATEDIFF(MONTH, e.fecha_contratacion, @fecha_diciembre) * 22
                    ELSE 264
                END
            -- Si el empleado fue contratado después del 20 de diciembre del año anterior, calcular para el año siguiente
            ELSE 
                -- Calcular el aguinaldo proporcional para el año siguiente desde el 1 de enero hasta el 20 de diciembre
                -- También limitando el cálculo a un máximo de 264 días trabajados
                (s.salario_base / 365) * 
                CASE 
                    WHEN DATEDIFF(MONTH, CAST(CAST(@anio AS VARCHAR(4)) + '-01-01' AS DATE), @fecha_diciembre) * 22 <= 264
                    THEN DATEDIFF(MONTH, CAST(CAST(@anio AS VARCHAR(4)) + '-01-01' AS DATE), @fecha_diciembre) * 22
                    ELSE 264
                END
        END AS monto
    FROM 
        Empleado e
    INNER JOIN 
        Salario s ON e.id_empleado = s.fk_id_empleado
    INNER JOIN 
        Empresa em ON e.fk_id_empresa = em.id_empresa
    WHERE 
        e.fk_id_empresa = @id_empresa
        -- Solo empleados contratados antes del 20 de diciembre del año en curso o que se calculen para los años siguientes
        AND (e.fecha_contratacion <= @fecha_diciembre 
             OR (YEAR(e.fecha_contratacion) < @anio AND e.fecha_contratacion > CAST(CAST(@anio - 1 AS VARCHAR(4)) + '-12-20' AS DATE)))
        AND NOT EXISTS (
            -- Asegurar que no haya un Aguinaldo ya registrado para este empleado en el mismo año
            SELECT 1 
            FROM Aguinaldo a 
            WHERE a.fk_id_empleado = e.id_empleado 
            AND YEAR(a.fecha) = @anio
        );
END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_aguinaldo
    @id_aguinaldo INT,        -- ID del aguinaldo específico
    @eliminar_todos BIT = 0   -- Indica si se eliminan todos los aguinaldos del empleado (1 = Sí, 0 = No)
AS
BEGIN
    SET NOCOUNT ON;

    IF @eliminar_todos = 1
    BEGIN
        -- Eliminar todos los aguinaldos del empleado relacionado con @id_aguinaldo
        DELETE FROM Aguinaldo
        WHERE fk_id_empleado = (SELECT fk_id_empleado FROM Aguinaldo WHERE id_aguinaldo = @id_aguinaldo);

        SELECT 'Todos los aguinaldos del empleado fueron eliminados.' AS Resultado;
    END
    ELSE
    BEGIN
        -- Eliminar solo el aguinaldo específico
        DELETE FROM Aguinaldo
        WHERE id_aguinaldo = @id_aguinaldo;

        SELECT 'El aguinaldo específico fue eliminado.' AS Resultado;
    END
END;
GO

--Liquidacion
--Crear
CREATE PROCEDURE sp_crear_liquidacion
    @dpi_pasaporte NVARCHAR(20),     -- DPI/Pasaporte del empleado
    @fecha_fin_contrato DATE,        -- Fecha fin del contrato
    @fecha DATE = NULL               -- Fecha de liquidación (predeterminada a hoy si es NULL)
AS
BEGIN
    -- Establecer la fecha actual si @fecha es NULL
    SET @fecha = ISNULL(@fecha, GETDATE());

    DECLARE @id_empleado INT;
    DECLARE @monto_liquidacion DECIMAL(10,2);

    -- Obtener ID del empleado basado en DPI/Pasaporte
    SELECT @id_empleado = id_empleado
    FROM Empleado
    WHERE dpi_pasaporte = @dpi_pasaporte;

    IF @id_empleado IS NULL
    BEGIN
        RAISERROR('Empleado no encontrado.', 16, 1);
        RETURN;
    END;

    -- Calcular el monto de liquidación usando el procedimiento sp_calcular_liquidacion
    DECLARE @liquidacion_total TABLE (id_empleado INT, Liquidacion_Total DECIMAL(10, 2));

    INSERT INTO @liquidacion_total
    EXEC sp_calcular_liquidacion @dpi_pasaporte;

    -- Obtener el monto calculado
    SELECT @monto_liquidacion = Liquidacion_Total 
    FROM @liquidacion_total 
    WHERE id_empleado = @id_empleado;

    -- Insertar en la tabla Liquidacion
    INSERT INTO Liquidacion (fecha_fin_contrato, fecha, monto, fk_id_empleado)
    VALUES (@fecha_fin_contrato, @fecha, @monto_liquidacion, @id_empleado);

    -- Actualizar el estado del empleado a ID 5 (liquidado)
    UPDATE Empleado
    SET fk_id_estado = 5
    WHERE id_empleado = @id_empleado;
END;
GO

--Listar
CREATE PROCEDURE sp_listar_liquidacion
    @criterio NVARCHAR(255) = NULL  -- Criterio de búsqueda general (opcional)
AS
BEGIN
    SELECT 
        l.id_liquidacion,
        e.id_empleado,
        e.nombres + ' ' + e.apellidos AS Nombre,
        e.puesto AS Puesto,
        l.fecha_fin_contrato,
        l.fecha AS Fecha_Liquidacion,
        l.monto AS Monto_Liquidacion
    FROM 
        Liquidacion l
    INNER JOIN 
        Empleado e ON l.fk_id_empleado = e.id_empleado
    WHERE
        @criterio IS NULL OR
        (e.nombres + ' ' + e.apellidos LIKE '%' + @criterio + '%' 
         OR e.puesto LIKE '%' + @criterio + '%')
    ORDER BY 
        l.fecha DESC;
END;
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_liquidacion
    @id INT,              -- ID de la liquidación
    @idEmpleado INT       -- ID del empleado
AS
BEGIN
    -- Verificar que el empleado existe
    IF NOT EXISTS (SELECT 1 FROM Empleado WHERE id_empleado = @idEmpleado)
    BEGIN
        RAISERROR('Empleado no encontrado.', 16, 1);
        RETURN;
    END

    -- Eliminar el registro de liquidación
    DELETE FROM Liquidacion
    WHERE id_liquidacion = @id AND fk_id_empleado = @idEmpleado;

    -- Actualizar el estado del empleado a ID 1 (activo)
    UPDATE Empleado
    SET fk_id_estado = 1
    WHERE id_empleado = @idEmpleado;
END;



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
('Permiso'),
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
