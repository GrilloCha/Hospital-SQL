CREATE DATABASE BASEHOSPITAL1;
GO
USE BASEHOSPITAL1;
GO

-- Tabla de tipos de usuario
CREATE TABLE TipoUsuario (
    idTipoUsuario INT NOT NULL PRIMARY KEY,
    tipoUsuario VARCHAR(25) NOT NULL
);

-- Usuarios generales
CREATE TABLE Usuario (
    idUsuario INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    idTipoUsuario INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidoPaterno VARCHAR(50) NOT NULL,
    apellidoMaterno VARCHAR(50),
    contrasenaLogin VARCHAR(50) NOT NULL,
    fechaNacimiento DATETIME,
    curp VARCHAR(18),
    domicilio VARCHAR(50),
    celularContacto VARCHAR(12),
    correoElectronico VARCHAR(50),
    CONSTRAINT FK_Usuario_TipoUsuario FOREIGN KEY(idTipoUsuario)
        REFERENCES TipoUsuario(idTipoUsuario) ON UPDATE CASCADE
);

-- Pacientes (derivado de Usuario)
CREATE TABLE Paciente (
    idPaciente INT IDENTITY(1,1) PRIMARY KEY,
    idUsuario INT NOT NULL,
    CONSTRAINT FK_Paciente_Usuario FOREIGN KEY(idUsuario)
        REFERENCES Usuario(idUsuario) ON UPDATE CASCADE
);

-- Tabla de tipos de empleado (1 = recepcionista, 2 = doctor)
CREATE TABLE TipoEmpleado (
    idTipoEmpleado INT NOT NULL PRIMARY KEY,
    tipoEmpleado VARCHAR(15) NOT NULL
);

-- Empleados (derivado de Usuario)
CREATE TABLE Empleado (
    idEmpleado INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    idTipoEmpleado INT NOT NULL,
    idUsuario INT NOT NULL,
    salario MONEY NOT NULL,
    CONSTRAINT FK_Empleado_TipoEmpleado FOREIGN KEY(idTipoEmpleado)
        REFERENCES TipoEmpleado(idTipoEmpleado) ON UPDATE CASCADE,
    CONSTRAINT FK_Empleado_Usuario FOREIGN KEY(idUsuario)
        REFERENCES Usuario(idUsuario) ON UPDATE CASCADE
);

-- Doctores (derivado de Empleado)
CREATE TABLE Especialidad (
    idEspecialidad INT NOT NULL PRIMARY KEY,
    nombreEspecialidad VARCHAR(30) NOT NULL,
    costo MONEY NOT NULL
);

CREATE TABLE Doctor (
    idDoctor INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    idEmpleado INT NOT NULL,
    idEspecialidad INT NOT NULL,
    cedulaProfesional VARCHAR(50) NOT NULL,
    estatusActividad BIT NOT NULL,
    CONSTRAINT FK_Doctor_Empleado FOREIGN KEY(idEmpleado)
        REFERENCES Empleado(idEmpleado) ON UPDATE CASCADE,
    CONSTRAINT FK_Doctor_Especialidad FOREIGN KEY(idEspecialidad)
        REFERENCES Especialidad(idEspecialidad) ON UPDATE CASCADE
);

-- Recepcionistas (derivado de Empleado)
CREATE TABLE Recepcionista (
    idEmpleado INT PRIMARY KEY,
    CONSTRAINT FK_Recepcionista_Empleado FOREIGN KEY(idEmpleado)
        REFERENCES Empleado(idEmpleado) ON UPDATE CASCADE
);

-- Jornada laboral
CREATE TABLE JornadaLaboral (
    idEmpleado INT NOT NULL,
    diaSemana VARCHAR(10),
    horaInicio TIME,
    horaFin TIME,
    PRIMARY KEY (idEmpleado, diaSemana),
    FOREIGN KEY (idEmpleado) REFERENCES Empleado(idEmpleado) ON UPDATE CASCADE
);

-- Consultorios
CREATE TABLE Consultorio (
    idConsultorio INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    idEspecialidad INT NOT NULL,
    CONSTRAINT Fk_Consultorio_Especialidad FOREIGN KEY(idEspecialidad)
        REFERENCES Especialidad(idEspecialidad) ON UPDATE CASCADE
);
SET IDENTITY_INSERT Consultorio ON;
-- Citas
CREATE TABLE Cita (
    folioCita INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    idDoctor INT NOT NULL,
    idPaciente INT NOT NULL,
    fechaCita DATE NOT NULL,
    CONSTRAINT FK_Cita_Paciente FOREIGN KEY(idPaciente)
        REFERENCES Paciente(idPaciente),
    CONSTRAINT FK_Cita_Doctor FOREIGN KEY(idDoctor)
        REFERENCES Doctor(idDoctor) 
);
ALTER TABLE Cita
ADD estatusCita VARCHAR(20) NOT NULL DEFAULT 'Pendiente';
ALTER TABLE Cita
ADD horaCita TIME NOT NULL;

-- Pago de cita
CREATE TABLE PagoCita (
    idPagoCita INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    folioCita INT NOT NULL,
    estatusPago BIT NOT NULL,
    CONSTRAINT FK_PagoCita_Cita FOREIGN KEY(folioCita)
        REFERENCES Cita(folioCita) ON UPDATE CASCADE
);

-- Recetas
CREATE TABLE Receta (
    folioReceta INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    idDoctor INT NOT NULL,
    idPaciente INT NOT NULL,
    folioCita INT NOT NULL,
    fechaExpedicion DATETIME NOT NULL,
    diagnostico VARCHAR(100),
    tratamiento VARCHAR(100),
    observaciones VARCHAR(100),
    CONSTRAINT FK_Receta_Doctor FOREIGN KEY(idDoctor)
        REFERENCES Doctor(idDoctor),
    CONSTRAINT FK_Receta_Paciente FOREIGN KEY(idPaciente)
        REFERENCES Paciente(idPaciente),
    CONSTRAINT FK_Receta_Cita FOREIGN KEY(folioCita)
        REFERENCES Cita(folioCita)
);

-- Servicios y medicamentos
CREATE TABLE Servicio (
    idServicio INT NOT NULL PRIMARY KEY,
    preciosServicio MONEY NOT NULL,
    nombreServicio VARCHAR(50) NOT NULL
);

CREATE TABLE Medicamento (
    idMedicamento INT NOT NULL PRIMARY KEY,
    precioMedicamento MONEY NOT NULL,
    nombre VARCHAR(50) NOT NULL
);

-- Tickets y pagos
-- Tabla principal de Tickets con llave primaria compuest
-- Tabla principal de Tickets con llave primaria compuesta
CREATE TABLE Ticket (
    fechaTicket DATE NOT NULL,
    nombrePaciente VARCHAR(255) NOT NULL,
    doctorResponsable VARCHAR(255),
    idUsuario INT NOT NULL,
    totalTicket DECIMAL(10,2) NOT NULL DEFAULT 0,
    horaTicket TIME NOT NULL DEFAULT GETDATE(),
    
    -- Llave primaria compuesta
    CONSTRAINT PK_Ticket PRIMARY KEY (fechaTicket, nombrePaciente),
    
    -- Llave foránea
    CONSTRAINT FK_Ticket_Usuario FOREIGN KEY(idUsuario) 
        REFERENCES Usuario(idUsuario) ON UPDATE CASCADE
);

-- Tabla de detalle para medicamentos del ticket
CREATE TABLE TicketMedicamento (
    fechaTicket DATE NOT NULL,
    nombrePaciente VARCHAR(255) NOT NULL,
    idMedicamento INT NOT NULL,
    precioMedicamento DECIMAL(10,2) NOT NULL,
    
    -- Llave primaria compuesta
    CONSTRAINT PK_TicketMedicamento PRIMARY KEY (fechaTicket, nombrePaciente, idMedicamento),
    
    -- Llaves foráneas
    CONSTRAINT FK_TicketMedicamento_Ticket FOREIGN KEY (fechaTicket, nombrePaciente) 
        REFERENCES Ticket(fechaTicket, nombrePaciente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FK_TicketMedicamento_Medicamento FOREIGN KEY (idMedicamento) 
        REFERENCES Medicamento(idMedicamento) ON UPDATE CASCADE
);

-- Tabla de detalle para servicios del ticket
CREATE TABLE TicketServicio (
    fechaTicket DATE NOT NULL,
    nombrePaciente VARCHAR(255) NOT NULL,
    idServicio INT NOT NULL,
    precioServicio DECIMAL(10,2) NOT NULL,
    
    -- Llave primaria compuesta
    CONSTRAINT PK_TicketServicio PRIMARY KEY (fechaTicket, nombrePaciente, idServicio),
    
    -- Llaves foráneas
    CONSTRAINT FK_TicketServicio_Ticket FOREIGN KEY (fechaTicket, nombrePaciente) 
        REFERENCES Ticket(fechaTicket, nombrePaciente) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT FK_TicketServicio_Servicio FOREIGN KEY (idServicio) 
        REFERENCES Servicio(idServicio) ON UPDATE CASCADE
);

-- Índices para mejorar el rendimiento
CREATE INDEX IX_Ticket_Usuario ON Ticket(idUsuario);
CREATE INDEX IX_Ticket_Fecha ON Ticket(fechaTicket);
CREATE INDEX IX_TicketMedicamento_Medicamento ON TicketMedicamento(idMedicamento);
CREATE INDEX IX_TicketServicio_Servicio ON TicketServicio(idServicio);

CREATE TABLE Bitacora(
    idHistorial INT IDENTITY(1,1) PRIMARY KEY,
    idPaciente INT,
    idEmpleado INT,
    folioCita INT,
    fechaCita DATETIME NOT NULL,
    CONSTRAINT FK_Bitacora_Paciente FOREIGN KEY (idPaciente) 
    REFERENCES Paciente(idPaciente),
    CONSTRAINT FK_Bitacora_Empleado FOREIGN KEY (idEmpleado) 
    REFERENCES Empleado(idEmpleado),
    CONSTRAINT FK_Bitacora_Cita FOREIGN KEY (folioCita) 
    REFERENCES Cita(folioCita)
);
