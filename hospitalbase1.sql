
SELECT D.idDoctor, D.idEmpleado, JL.*
FROM Doctor D
JOIN JornadaLaboral JL ON D.idEmpleado = JL.idEmpleado
WHERE D.idDoctor = 1; -- Cambia al idDoctor que est?s usando

SELECT * FROM Doctor

SELECT u.idUsuario, u.idTipoUsuario, u.nombre, u.apellidoPaterno, 
               u.apellidoMaterno, u.contrasenaLogin, tu.tipoUsuario,
               e.idEmpleado, e.idTipoEmpleado, te.tipoEmpleado AS rolEmpleado
        FROM Usuario u
        INNER JOIN TipoUsuario tu ON u.idTipoUsuario = tu.idTipoUsuario
        LEFT JOIN Empleado e ON u.idUsuario = e.idUsuario
        LEFT JOIN TipoEmpleado te ON e.idTipoEmpleado = te.idTipoEmpleado
        WHERE u.correoElectronico = 'charly.artulio001@gmail.com'







