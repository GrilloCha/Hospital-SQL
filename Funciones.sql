-- FUNCION OBTENER EDAD de un usuario
CREATE FUNCTION dbo.ObtenerEdadUsuario (@idUsuario INT)
RETURNS INT
AS
BEGIN
    DECLARE @Edad INT;
    DECLARE @FechaNacimiento DATE;

    -- Obtener la fecha de nacimiento desde la tabla Usuario
    SELECT @FechaNacimiento = fechaNacimiento
    FROM Usuario
    WHERE idUsuario = @idUsuario;

    -- Calcular la edad
    SET @Edad = DATEDIFF(YEAR, @FechaNacimiento, GETDATE());

    -- Ajustar si a?n no ha cumplido a?os este a?o
    IF (MONTH(@FechaNacimiento) > MONTH(GETDATE()))
       OR (MONTH(@FechaNacimiento) = MONTH(GETDATE()) AND DAY(@FechaNacimiento) > DAY(GETDATE()))
    BEGIN
        SET @Edad = @Edad - 1;
    END

    RETURN @Edad;
END;

select dbo.ObtenerEdadUsuario(2) AS Edad




-- Funcion que cuenta las citas de un doctor en la semana Actual 
CREATE FUNCTION dbo.ContarCitasSemanaActual (@idPaciente INT)
RETURNS INT
AS
BEGIN
    DECLARE @Total INT;
    DECLARE @Hoy DATE = CAST(GETDATE() AS DATE);

    -- Calcular el inicio de semana (lunes) 
    DECLARE @DiaSemana INT = DATEPART(WEEKDAY, @Hoy);
    DECLARE @InicioSemana DATE = DATEADD(DAY, -(CASE 
                                                    WHEN @DiaSemana = 1 THEN 6  -- domingo
                                                    ELSE @DiaSemana - 2         -- lunes = 1, etc.
                                                END), @Hoy);

    DECLARE @FinSemana DATE = DATEADD(DAY, 6, @InicioSemana);

    SELECT @Total = COUNT(*)
    FROM Cita
    WHERE idPaciente = @idPaciente
      AND fechaCita >= @InicioSemana
      AND fechaCita <= @FinSemana;

    RETURN @Total;
END;

select dbo.ContarCitasSemanaActual(5) AS citas;


--funcion contar citas pendientes de un paciente 
CREATE FUNCTION dbo.CitasPendientesPaciente (@idPaciente INT)
RETURNS INT
AS
BEGIN
    RETURN (
        SELECT COUNT(*) FROM Cita
        WHERE idPaciente = @idPaciente
          AND fechaCita >= GETDATE()
          AND estatusCita = 1  
    );
END;




CREATE FUNCTION dbo.CalcularTotalCuenta (@folioCita INT)
RETURNS DECIMAL(10, 2)
AS
BEGIN
    DECLARE @total DECIMAL(10,2) = 0;
    DECLARE @idEspecialidad INT;

   
    SELECT @idEspecialidad = d.idEspecialidad
    FROM Cita c
    JOIN Doctor d ON c.idDoctor = d.idDoctor
    WHERE c.folioCita = @folioCita;

    SELECT @total = ISNULL(e.costo, 0)
    FROM Especialidad e
    WHERE e.idEspecialidad = @idEspecialidad;

    RETURN @total;
END;
