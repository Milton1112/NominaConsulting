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
END
GO


--Usuarios

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
END
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
END


--empleado

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
END
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
END
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
END
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
END
GO

--Eliminar
CREATE PROCEDURE sp_eliminar_usuario
@id INT
AS
BEGIN
    SET NOCOUNT ON;

	DELETE FROM Usuario
	WHERE id_usuario = @id;

END
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
END
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

    
END
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
END
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

END
GO


--listar empresa
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
END
GO;

-- INSERTAR DATOS EN LOS SP
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
    @Expediente = NULL,  -- Documento en Expediente como NULL
    @Fk_Id_Oficina = 1,
    @Fk_Id_Profesion = 1,
    @Fk_Id_Departamento = 1,
    @Fk_Id_Rol = 4,
    @Fk_Id_Estado = 1,
    @Fk_Id_Empresa = 1;

-- INSERTAR USUARIO
EXEC sp_registrar_usuario 
    @correo = 'admin@consulting-nomina.com',
    @contrasena = 'Holaque123',
    @fk_id_empleado = 1,
    @fk_id_empresa = 1;
