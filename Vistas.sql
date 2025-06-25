--VISTAS CITAS PACIENTE
CREATE VIEW VistaCitasPaciente AS
SELECT 
    c.folioCita,
    c.fechaCita,
    u.nombre,
    u.apellidoPaterno,
    u.apellidoMaterno,
    d.cedulaProfesional,
    c.estatusCita,
    c.idPaciente
FROM Cita c
JOIN Doctor d ON c.idDoctor = d.idDoctor
JOIN Empleado e ON d.idEmpleado = e.idEmpleado
JOIN Usuario u ON e.idUsuario = u.idUsuario;


SELECT 
    c.folioCita,
    c.fechaCita,
    c.estatusCita,
    u.nombre AS nombrePaciente,
    u.apellidoPaterno,
    u.apellidoMaterno
FROM Cita c
JOIN Paciente p ON c.idPaciente = p.idPaciente
JOIN Usuario u ON p.idUsuario = u.idUsuario
WHERE c.idDoctor = @idDoctor
  AND c.fechaCita > GETDATE()
ORDER BY c.fechaCita;

--VISTA PARA PROXIMAS CITAS DOCTORES
CREATE VIEW VistaProximasCitasDoctores AS
SELECT 
    c.folioCita,
    c.fechaCita,
    c.estatusCita,
    c.idDoctor,
    u.nombre AS nombrePaciente,
    u.apellidoPaterno,
    u.apellidoMaterno
FROM Cita c
JOIN Paciente p ON c.idPaciente = p.idPaciente
JOIN Usuario u ON p.idUsuario = u.idUsuario
WHERE c.fechaCita > GETDATE();

--Vista datos personales Paciente
CREATE VIEW VistaDatosPersonalesPaciente AS
SELECT 
    p.idPaciente, u.nombre, u.apellidoPaterno, u.apellidoMaterno, u.fechaNacimiento, u.curp , u.correoElectronico,
    u.celularContacto, u.domicilio
FROM 
    Paciente p
INNER JOIN 
    Usuario u ON p.idUsuario = u.idUsuario;

	-- vista datos personales DOCTOR
	CREATE VIEW VistaDatosPersonalesDoctor AS
SELECT 
    d.idDoctor, u.nombre, u.apellidoPaterno, u.apellidoMaterno, u.fechaNacimiento, u.curp , u.correoElectronico,
    u.celularContacto, u.domicilio, d.cedulaProfesional
FROM 
    Doctor d
INNER JOIN Empleado e ON d.idEmpleado = e.idEmpleado
INNER JOIN Usuario u ON e.idUsuario = u.idUsuario;

SELECT * FROM Usuario