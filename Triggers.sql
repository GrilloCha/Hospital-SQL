--Trigger creacion de Usuario a Paciente
CREATE TRIGGER trg_InsertPaciente
ON Usuario
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    INSERT INTO Paciente (idUsuario)
    SELECT idUsuario
    FROM inserted
    WHERE idTipoUsuario = 1;
END;

--Trigger creacion de Usuario a Doctor o Recepcionista
CREATE OR ALTER TRIGGER trg_InsertRolEmpleado
ON Empleado
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Insertar recepcionistas
    INSERT INTO Recepcionista (idEmpleado)
    SELECT idEmpleado
    FROM inserted
    WHERE idTipoEmpleado = 1;

    -- Insertar doctores con valores por defecto
    INSERT INTO Doctor (idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad)
    SELECT idEmpleado, 1, 'DEFAULT-CEDULA', 1
    FROM inserted
    WHERE idTipoEmpleado = 2;
END


-- 1. Insertar en Usuario
INSERT INTO Usuario (
    idTipoUsuario, nombre, apellidoPaterno, apellidoMaterno,
    contrasenaLogin, correoElectronico, curp,
    fechaNacimiento, domicilio, celularContacto
)
VALUES (
    2, -- Empleado
    'Laura', 'Ramírez', 'Soto',
    'claveLaura2024', 'laura.ramirez@hospital.com', 'CURPLAURA2024RAMI',
    '1992-09-10', 'Calle Palma 432', '5556789123'
);

-- 2. Obtener idUsuario recién insertado
DECLARE @idUsuario INT;
SELECT @idUsuario = SCOPE_IDENTITY();

-- 3. Insertar en Empleado
INSERT INTO Empleado (
    idUsuario, idTipoEmpleado, salario
)
VALUES (
    @idUsuario, 1, 9500 -- 1 = Recepcionista
);

SELECT nombre, e.idUsuario FROM Usuario u 
JOIN Empleado e ON u.idUsuario = e.idUsuario
WHERE e.idTipoEmpleado = '1'

SELECT * FROM Usuario



