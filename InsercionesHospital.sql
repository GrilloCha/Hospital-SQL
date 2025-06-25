--Insertar tipo de usuario
INSERT INTO tipousuario (idTipoUsuario, tipoUsuario) VALUES
(1, 'Paciente'),
(2, 'Empleado')

--Insertar tipo de empleado

INSERT INTO TipoEmpleado(idTipoEmpleado, tipoEmpleado) VALUES
(1, 'Recepcionista'),
(2, 'Doctor'),
(3, 'Farmacia')

--Insertamos algunos usuarios manualmente

INSERT INTO usuario (
    idTipoUsuario,
    nombre,
    apellidoPaterno,
    apellidoMaterno,
    contrasenaLogin,
    fechaNacimiento,
    curp,
    domicilio,
    celularContacto,
    correoElectronico
)
VALUES (
    1, -- idTipoUsuario: Paciente
    'Carlos',
    'Ram?rez',
    'L?pez',
    'clave123',
    '1990-05-10',
    'CARR900510HDFRMN01',
    'Av. Reforma 123',
    '5512345678',
    'carlos.ramirez@gmail.com'
);


INSERT INTO usuario (
    idTipoUsuario,
    nombre,
    apellidoPaterno,
    apellidoMaterno,
    contrasenaLogin,
    fechaNacimiento,
    curp,
    domicilio,
    celularContacto,
    correoElectronico
)
VALUES (
    1, -- idTipoUsuario: Empleado
    'Fernando',
    'Oropeza',
    'Rodriguez',
    'FerRodri',
    '1979-06-11',
    'OORF790611HDFRMN02',
    'Centro Historico 12',
    '511234563',
    'fer.oro@gmail.com'
);


INSERT INTO usuario ( idTipoUsuario, nombre, apellidoPaterno, apellidoMaterno,
contrasenaLogin, fechaNacimiento, curp, domicilio, celularContacto, correoElectronico)
VALUES (
    1, -- idTipoUsuario: Empleado
    'Jorge', 'Jimenez', 'Garcia', 'Jorge123', '1970-03-04',
	'JIGF700304HDFRMN01', 'Pantitlan 181', '5567384950', 'jrg7003@hootmail.com');


--Insecion Empleado
	 INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (2, 2, 20000)

	  INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (2, 9, 25000)

	 INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (2, 8, 30000)

	 INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (2, 10, 30000)
	 
	 INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (1, 11, 15000)

	 INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (2, 17, 35000)

	 INSERT INTO Empleado (idTipoEmpleado, idUsuario, salario)
	 VALUES (1, 16, 15000)


--Insercion especialidades

INSERT INTO Especialidad (
	idEspecialidad,
	nombreEspecialidad,
	costo
	)
	VALUES (
	1,
	'Cardiologia',
	2000
	)

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (2 , 'Dermatologia', 950 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (3 , 'Ginecologia', 850 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (4 , 'Medicina General', 300 )
	
	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (5 , 'Nefrologia', 1500 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (6 , 'Neutriologia', 1000 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (7 , 'Oftamologia', 750 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (8 , 'Oncologia', 1500 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (9 , 'Ortopedia', 700 )

	INSERT INTO Especialidad ( idEspecialidad, nombreEspecialidad, costo )
	VALUES (10 , 'Pediatria', 850 )

--Insercion consultorios
INSERT INTO Consultorio( idConsultorio, idEspecialidad )
	VALUES (01 , 1 )

	INSERT INTO Consultorio( idConsultorio, idEspecialidad )
	VALUES (02 , 2 )

	INSERT INTO Consultorio( idConsultorio, idEspecialidad )
	VALUES (9 , 3 )

	INSERT INTO Consultorio( idConsultorio, idEspecialidad )
	VALUES (03 , 4 )

	INSERT INTO Consultorio( idConsultorio, idEspecialidad )
	VALUES (10 , 5 )

--Insercion Doctor
	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (3, 1, 15287E, 1) 

	 INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (4, 3, 108434, 1) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (2, 5, 345675, 0) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (8, 2, 155879, 1) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (10, 6, 89765, 1) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (13, 4, 875632, 1) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (19, 7, 34567, 1) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (20, 8, 56748, 1) 

	INSERT INTO Doctor ( idEmpleado, idEspecialidad, cedulaProfesional, estatusActividad )
	VALUES (2, 2, 345675, 0) 

	 

--Insercion Recepcionistas
	 INSERT INTO Recepcionista(idEmpleado)
	 VALUES (5)
	 INSERT INTO Recepcionista(idEmpleado)
	 VALUES (9)
	 INSERT INTO Recepcionista(idEmpleado)
	 VALUES (11)

INSERT INTO Usuario 
    (idTipoUsuario, nombre, apellidoPaterno, apellidoMaterno, contrasenaLogin, correoElectronico, curp, fechaNacimiento, domicilio, celularContacto)
VALUES
    (2, 'Ana', 'Gómez', 'López', 'Contrasena123', 'ana.gomez@hospital.com', 'GOLA850101HDFRNS09', '1985-01-01', 'Calle Falsa 123', '5512345678');


--Insertamos paciente
	INSERT INTO Paciente (idUsuario)
	SELECT idUsuario
	FROM Usuario
	WHERE idTipoUsuario = 1
	  AND idUsuario NOT IN (SELECT idUsuario FROM Paciente);

	INSERT INTO Usuario (
    idTipoUsuario,
    nombre,
    apellidoPaterno,
    apellidoMaterno,
    contrasenaLogin,
    fechaNacimiento,
    curp,
    domicilio,
    celularContacto,
    correoElectronico
)
VALUES (
    1, -- Paciente
    'Carlos Arturo',
    'Torres',
    'Tellez',
    '123456',
    '2000-09-24',
    'TOTC000924HDFRLRA5',
    'Calle Ficticia 123',
    '5634782453',
    'charly.artulio001@gmail.com'
);

INSERT INTO Paciente (idUsuario)
SELECT idUsuario
FROM Usuario
WHERE correoElectronico = 'charly.artulio001@gmail.com'
  AND idTipoUsuario = 1
  AND idUsuario NOT IN (SELECT idUsuario FROM Paciente);

-- Ahora lo insertamos también como paciente si no existe aún:
INSERT INTO Paciente (idUsuario)
SELECT idUsuario
FROM Usuario
WHERE correoElectronico = 'charly.artulio001@gmail.com'



--Insercion cita manual
INSERT INTO Cita (idDoctor, idPaciente, fechaCita, horaCita, estatusCita)
VALUES (4, 1, '2025-06-16', '10:00', 'Pendiente');

--Insercion Jornada laboral
INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (4, 'Monday', '09:00', '17:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (4, 'Tuesday', '09:00', '17:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (4, 'Thursday', '09:00', '17:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (2, 'Monday', '08:30', '15:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (2, 'Tuesday', '09:00', '17:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (2, 'Wednesday', '10:00', '18:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (3, 'Thursday', '07:00', '13:30');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (3, 'Friday', '08:00', '15:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (3, 'Saturday', '09:00', '14:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (13, 'Wednesday', '09:00', '18:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (8, 'Monday', '08:30', '15:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (19, 'Friday', '09:30', '18:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (20, 'Monday', '08:30', '18:00');

INSERT INTO JornadaLaboral (idEmpleado, diaSemana, horaInicio, horaFin)
VALUES (10, 'Monday', '08:00', '18:00');


