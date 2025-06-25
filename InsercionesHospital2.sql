
-- Asegúrate de tener una especialidad registrada, por ejemplo:
-- INSERT INTO Especialidad (nombreEspecialidad) VALUES ('Cardiología');
-- Suponemos que esa especialidad tiene id = 1

-- 1. Insertar en Usuario
INSERT INTO Usuario (
    idTipoUsuario, nombre, apellidoPaterno, apellidoMaterno,
    contrasenaLogin, correoElectronico, curp,
    fechaNacimiento, domicilio, celularContacto
) VALUES (
    2, 'Luis', 'Fernández', 'Gómez',
    'doc123', 'luis.fernandez@hospital.com', 'CURPDOCLF12345678',
    '1980-02-15', 'Av. Reforma 200', '5544336677'
);

-- 2. Insertar en Empleado con salario
INSERT INTO Empleado (idUsuario, idTipoEmpleado, salario)
SELECT idUsuario, 2, 20000.00
FROM Usuario
WHERE correoElectronico = 'luis.fernandez@hospital.com';

-- 3. Insertar en Doctor
INSERT INTO Doctor (idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad)
SELECT e.idEmpleado, 1, 'CEDULA987654', 1
FROM Empleado e
JOIN Usuario u ON e.idUsuario = u.idUsuario
WHERE u.correoElectronico = 'luis.fernandez@hospital.com';
