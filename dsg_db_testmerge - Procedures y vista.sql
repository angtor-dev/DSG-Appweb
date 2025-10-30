-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 04:40:46
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dsg_db_testmerge`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE PROCEDURE `sp_gestionar_asignacion_laboral` (IN `p_idTrabajador` INT, IN `p_idDepartamento` INT, IN `p_idTurno` INT, IN `p_idCargo` INT, IN `p_fechaAsignacion` DATE)   BEGIN
    -- Declarar variables para almacenar la asignación laboral actual del trabajador
    DECLARE v_current_id INT;
    DECLARE v_current_idDepartamento INT;
    DECLARE v_current_idTurno INT;
    DECLARE v_current_idCargo INT;

    -- Variables para capturar el SQLSTATE y el mensaje de error en el manejador de excepciones
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_message_text VARCHAR(255);
    
    -- Intentar obtener la asignación laboral actual (donde esActual = 1) para el trabajador
    -- El bloqueo FOR UPDATE ayuda a prevenir condiciones de carrera si múltiples procesos
    -- intentan modificar la misma asignación simultáneamente.
    SELECT
        id,
        idDivision,
        idTurno,
        idCargo
    INTO
        v_current_id,
        v_current_idDepartamento,
        v_current_idTurno,
        v_current_idCargo
    FROM
        asignacion_laboral
    WHERE
        idTrabajador = p_idTrabajador AND esActual = 1;
    -- FOR UPDATE; -- Bloquea la fila seleccionada para evitar que otras transacciones la modifiquen.

    -- Verificar si se encontró una asignación laboral actual para el trabajador
    IF v_current_id IS NOT NULL THEN
        -- Si existe una asignación actual, verificar si los nuevos datos son diferentes
        IF (v_current_idDepartamento != p_idDepartamento OR
            v_current_idTurno != p_idTurno OR
            v_current_idCargo != p_idCargo) THEN

            -- Si los datos son diferentes, finalizar la asignación actual
            UPDATE asignacion_laboral
            SET fechaFin = CURRENT_TIMESTAMP()
            WHERE id = v_current_id;

            -- Insertar la nueva asignación laboral
            
            
            
            INSERT INTO asignacion_laboral (idTrabajador, idDivision, idTurno, idCargo, fechaAsignacion)
            VALUES (p_idTrabajador, p_idDepartamento, p_idTurno, p_idCargo, COALESCE(p_fechaAsignacion, CURRENT_TIMESTAMP() ) );

            -- Si los datos son los mismos, no hacer nada
        END IF;
    ELSE
        -- Si no hay una asignación actual para el trabajador, insertar la nueva asignación
        INSERT INTO asignacion_laboral (idTrabajador, idDivision, idTurno, idCargo, fechaAsignacion)
        VALUES (p_idTrabajador, p_idDepartamento, p_idTurno, p_idCargo, COALESCE(p_fechaAsignacion, CURRENT_TIMESTAMP() ));

    END IF;

END$$

CREATE PROCEDURE `sp_registrar_asistencia` (IN `p_id_asistencia_inasistencia` INT, IN `p_fecha` DATE, IN `p_id_trabajador` INT, IN `p_tipo_registro` ENUM('Asistencia','Inasistencia'), IN `p_hora_entrada` TIME, IN `p_hora_salida` TIME, IN `p_tipo_inasistencia` ENUM('Injustificado','Vacaciones','Medico','Emergencia','Judicial','Enfermedad','Muerte De Un Familiar','Otro'), IN `p_descripcion` TEXT)   BEGIN
    DECLARE v_id_fecha_asistencia INT;
    DECLARE v_existe_asistencia INT;
    DECLARE v_cierre INT DEFAULT 0;
    DECLARE p_id_asignacion_laboral INT;
    
    
    
    IF p_id_asistencia_inasistencia = 0 THEN
    SET p_id_asistencia_inasistencia = NULL;
    END IF;
    
    
    -- obtener el p_id_asignacion_laboral
    
    SELECT al.id INTO p_id_asignacion_laboral 
    FROM asignacion_laboral al 
    WHERE al.idTrabajador = p_id_trabajador AND al.esActual = 1;
    
    IF p_id_asignacion_laboral IS NULL THEN
    	SIGNAL SQLSTATE '45000' 
         SET MESSAGE_TEXT = "Show::El trabajador no tiene asignación laboral.";
    END IF;
    
    
    
    -- Verificar si la fecha está cerrada
    SELECT id, cierre INTO v_id_fecha_asistencia, v_cierre 
    FROM fechaasistencia 
    WHERE fecha = p_fecha;
    
    IF v_cierre = 1 THEN
    	SIGNAL SQLSTATE '45000' 
         SET MESSAGE_TEXT = "Show::La fecha ya está cerrada y no se pueden hacer modificaciones.";
    END IF;
    
    -- Si no existe la fecha, crearla
    IF v_id_fecha_asistencia IS NULL THEN
        INSERT INTO fechaasistencia (fecha, cierre) VALUES (p_fecha, 0);
        SET v_id_fecha_asistencia = LAST_INSERT_ID();
    END IF;
    
    -- Verificar si es una actualización o un nuevo registro
    IF p_id_asistencia_inasistencia IS NOT NULL THEN
        -- Modo actualización
        
        -- Verificar que el registro exista
        SELECT COUNT(*) INTO v_existe_asistencia 
        FROM asistencia_inasistencia 
        WHERE id = p_id_asistencia_inasistencia;
        
        IF v_existe_asistencia = 0 THEN
        	SIGNAL SQLSTATE '45000' 
         	 SET MESSAGE_TEXT = "Show::El registro a actualizar no existe.";
        END IF;
        
        -- Eliminar el registro previo (asistencia o inasistencia) por cascada
        IF p_tipo_registro = 'Asistencia' THEN
            -- Insertar nuevo registro de asistencia
            INSERT INTO asistencia (idAsistencia_inasistencia, horaEntrada, horaSalida)
            VALUES (p_id_asistencia_inasistencia, p_hora_entrada, p_hora_salida)
            ON DUPLICATE KEY UPDATE 
                horaEntrada = p_hora_entrada,
                horaSalida = p_hora_salida;
                
            -- Eliminar posible registro de inasistencia
            DELETE FROM inasistencia WHERE idAsistencia_inasistencia = p_id_asistencia_inasistencia;
        ELSE
            -- Insertar nuevo registro de inasistencia
            INSERT INTO inasistencia (idAsistencia_inasistencia, tipo, descripcion)
            VALUES (p_id_asistencia_inasistencia, p_tipo_inasistencia, p_descripcion)
            ON DUPLICATE KEY UPDATE 
                tipo = p_tipo_inasistencia,
                descripcion = p_descripcion;
                
            -- Eliminar posible registro de asistencia
            DELETE FROM asistencia WHERE idAsistencia_inasistencia = p_id_asistencia_inasistencia;
        END IF;
        
    ELSE
        -- Modo nuevo registro
        
        -- Verificar que no exista ya un registro para esta fecha y asignación laboral
        SELECT COUNT(*) INTO v_existe_asistencia 
        FROM asistencia_inasistencia 
        WHERE idFechaAsistencia = v_id_fecha_asistencia 
        AND idAsignacionLaboral = p_id_asignacion_laboral;
        
        IF v_existe_asistencia > 0 THEN
        	SIGNAL SQLSTATE '45000' 
         	 SET MESSAGE_TEXT = "Show::Ya existe un registro para este trabajador en la fecha especificada.";
        END IF;
        
        -- Insertar en tabla asistencia_inasistencia
        INSERT INTO asistencia_inasistencia (idFechaAsistencia, idAsignacionLaboral)
        VALUES (v_id_fecha_asistencia, p_id_asignacion_laboral);
        
        SET p_id_asistencia_inasistencia = LAST_INSERT_ID();
        
        -- Insertar en la tabla correspondiente (asistencia o inasistencia)
        IF p_tipo_registro = 'Asistencia' THEN
            INSERT INTO asistencia (idAsistencia_inasistencia, horaEntrada, horaSalida)
            VALUES (p_id_asistencia_inasistencia, p_hora_entrada, p_hora_salida);
            
        ELSE
            INSERT INTO inasistencia (idAsistencia_inasistencia, tipo, descripcion)
            VALUES (p_id_asistencia_inasistencia, p_tipo_inasistencia, p_descripcion);
            
        END IF;
        
    END IF;
END$$

CREATE PROCEDURE `sp_registrar_asistencia_semanal` (IN `p_fecha` DATE, IN `p_cedula` VARCHAR(10) CHARSET utf8mb4, IN `p_codigo_asistencia_inasistencia` VARCHAR(50), IN `p_turno` VARCHAR(50), IN `p_tipo_inasistencia` INT, IN `p_descripcion` TEXT, IN `p_laborable` TINYINT)   BEGIN
    -- procedure
    -- parametros
    -- # p_fecha DATE
    -- # p_cedula varchar
    -- # p_codigo_asistencia_inasistencia varchar 36 codigo UUID
    -- # p_turno
    -- # p_tipo_inasistencia
    -- # p_descripcion
    -- # p_laborable

    -- # p_tipo_inasistencia determina si es asistencia o inasistencia con un valor NULL asistencia : inasistencia
    -- # v_hora_entrada se obtiene de p_turno
    -- # v_hora_salida se obtiene de p_turno
    -- # v_id_asistencia_inasistencia se obtiene de p_codigo_asistencia_inasistencia
    DECLARE v_id_fecha_asistencia INT;
    DECLARE v_existe_asistencia INT;
    DECLARE v_cierre INT DEFAULT 0;
    DECLARE v_id_asistencia_inasistencia INT;
    DECLARE v_id_asignacion_laboral INT;
    DECLARE v_hora_entrada TIME;
    DECLARE v_hora_salida TIME;
    DECLARE v_id_trabajador INT;
    DECLARE v_signal_Text TEXT;
    
    
    
    -- validar p_laborable
    IF p_laborable NOT IN (0, 1) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = "Show::Error en el parametro laborable de la fecha de asistencia, contacte al desarrollador.";
    END IF;
    
    

    -- obtener el v_id_trabajador
    
        SELECT id INTO v_id_trabajador FROM trabajador WHERE cedula = p_cedula;
        
        IF v_id_trabajador IS NULL THEN
            SET v_signal_Text = CONCAT("Show::El trabajador con cedula ", p_cedula, " no existe.");
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = v_signal_Text;
        END IF;
        
        
    
    
        -- obtener el v_id_asignacion_laboral
    
            SELECT al.id INTO v_id_asignacion_laboral 
            FROM asignacion_laboral al 
            WHERE al.idTrabajador = v_id_trabajador AND al.esActual = 1;
            
            IF v_id_asignacion_laboral IS NULL THEN
                SET v_signal_Text = CONCAT("Show::El trabajador con cedula ", p_cedula, " no tiene asignación laboral.");
                SIGNAL SQLSTATE '45000' 
                SET MESSAGE_TEXT = v_signal_Text;
            END IF;
    
        -- Verificar si la fecha está cerrada y si existe
            SELECT id, cierre INTO v_id_fecha_asistencia, v_cierre 
            FROM fechaasistencia 
            WHERE fecha = p_fecha;
            
            IF v_cierre = 1 THEN
                SIGNAL SQLSTATE '45000' 
                SET MESSAGE_TEXT = "Show::La fecha ya se encuentra cerrada.";
            END IF;
        -- si la fecha no esta registrada se crea
            IF v_id_fecha_asistencia IS NULL THEN
                INSERT INTO fechaasistencia (fecha) VALUES (p_fecha);
                SELECT LAST_INSERT_ID() INTO v_id_fecha_asistencia;
            END IF;
    
        -- si hay un codigo de asistencia_inasistencia es una modificacion
        IF p_codigo_asistencia_inasistencia IS NOT NULL THEN
            -- obtener el v_id_asistencia_inasistencia
            SELECT id INTO v_id_asistencia_inasistencia 
            FROM asistencia_inasistencia 
            WHERE codigoAsistencia = p_codigo_asistencia_inasistencia;

            -- si no existe el v_id_asistencia_inasistencia
            IF v_id_asistencia_inasistencia IS NULL THEN
                SIGNAL SQLSTATE '45000' 
                SET MESSAGE_TEXT = "Show::El codigo de asistencia_inasistencia no existe. Contacte al desarrollador.";
            END IF;

            -- si todo va bien se actualiza
            -- primero si el dia es no laborable se elimina se elimina la asistencia y las inasistencias
            -- luego se modifica el campo laborable de la tabla asistencia_inasistencia

            IF p_laborable = 0 THEN
                DELETE FROM asistencia WHERE idAsistencia_inasistencia = v_id_asistencia_inasistencia;
                DELETE FROM inasistencia WHERE idAsistencia_inasistencia = v_id_asistencia_inasistencia;
                UPDATE asistencia_inasistencia SET laborable = 0 WHERE id = v_id_asistencia_inasistencia;
            ELSE
                -- si es laborable se actualiza el campo laborable a 1 y se inserta la asistencia o la inasistencia
                UPDATE asistencia_inasistencia SET laborable = 1 WHERE id = v_id_asistencia_inasistencia;
                -- borro los datos de asistencia y inasistencia
                DELETE FROM asistencia WHERE idAsistencia_inasistencia = v_id_asistencia_inasistencia;
                DELETE FROM inasistencia WHERE idAsistencia_inasistencia = v_id_asistencia_inasistencia;
                IF p_tipo_inasistencia IS NULL THEN -- asistencia


                    -- horario de entrada y salida
                    SELECT horario_entrada, horario_salida INTO v_hora_entrada, v_hora_salida 
                    FROM turno 
                    WHERE codigo = p_turno;

                    -- inserto la asistencia

                    INSERT INTO asistencia (idAsistencia_inasistencia, horaEntrada, horaSalida) 
                    VALUES 
                    (v_id_asistencia_inasistencia, v_hora_entrada, v_hora_salida);
                
                ELSE -- inasistencia
                    INSERT INTO inasistencia (idAsistencia_inasistencia, tipo, descripcion) 
                    VALUES 
                    (v_id_asistencia_inasistencia, p_tipo_inasistencia, p_descripcion);
                END IF;
            END IF;
        ELSE -- si no hay un codigo de asistencia_inasistencia es un nuevo registro
            -- valido que efectivamente no exista ya un registro para esta fecha y asignación laboral
                SELECT COUNT(*) INTO v_existe_asistencia 
                FROM asistencia_inasistencia 
                WHERE idFechaAsistencia = v_id_fecha_asistencia 
                AND idAsignacionLaboral = v_id_asignacion_laboral;
                
                IF v_existe_asistencia > 0 THEN
                    SET v_signal_Text = CONCAT("Show::Ya existe un registro para este trabajador (CI:", p_cedula, ") en la fecha '", p_fecha,"'.");
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = v_signal_Text;
                END IF;
            
            -- Insertar en tabla asistencia_inasistencia
                INSERT INTO asistencia_inasistencia (idFechaAsistencia, idAsignacionLaboral, laborable)
                VALUES (v_id_fecha_asistencia, v_id_asignacion_laboral, p_laborable);
                
                SET v_id_asistencia_inasistencia = LAST_INSERT_ID();
            -- insertar la asistencia o la inasistencia
            IF p_laborable = 1 THEN
                IF p_tipo_inasistencia IS NULL THEN -- asistencia


                    -- horario de entrada y salida
                    SELECT horario_entrada, horario_salida INTO v_hora_entrada, v_hora_salida 
                    FROM turno 
                    WHERE codigo = p_turno;

                    -- inserto la asistencia

                    INSERT INTO asistencia (idAsistencia_inasistencia, horaEntrada, horaSalida) 
                    VALUES 
                    (v_id_asistencia_inasistencia, v_hora_entrada, v_hora_salida);
                
                ELSE -- inasistencia
                    INSERT INTO inasistencia (idAsistencia_inasistencia, tipo, descripcion) 
                    VALUES 
                    (v_id_asistencia_inasistencia, p_tipo_inasistencia, p_descripcion);
                END IF;
            END IF;
        END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_asistencias`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_asistencias` (
`cedula` varchar(10)
,`nombre` varchar(50)
,`apellido` varchar(50)
,`fecha` date
,`horaEntrada` time
,`horaSalida` time
,`tipo` enum('Injustificado','Vacaciones','Medico','Emergencia','Judicial','Enfermedad','Muerte De Un Familiar','Otro')
,`descripcion` text
,`idDivision` int(11)
,`division` varchar(50)
,`idTurno` int(11)
,`turno` varchar(50)
,`esAsistencia` int(1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_asistencias`
--
DROP TABLE IF EXISTS `vista_asistencias`;

CREATE ALGORITHM=TEMPTABLE SQL SECURITY DEFINER VIEW `vista_asistencias`  AS SELECT `t`.`cedula` AS `cedula`, `t`.`nombre` AS `nombre`, `t`.`apellido` AS `apellido`, `fa`.`fecha` AS `fecha`, `a`.`horaEntrada` AS `horaEntrada`, `a`.`horaSalida` AS `horaSalida`, `i`.`tipo` AS `tipo`, `i`.`descripcion` AS `descripcion`, `d`.`id` AS `idDivision`, `d`.`nombre` AS `division`, `tu`.`id` AS `idTurno`, `tu`.`nombre` AS `turno`, if(`a`.`idAsistencia_inasistencia` is not null,1,if(`i`.`idAsistencia_inasistencia` is not null,0,NULL)) AS `esAsistencia` FROM (((((((`asistencia_inasistencia` `ai` join `fechaasistencia` `fa` on(`fa`.`id` = `ai`.`idFechaAsistencia`)) join `asignacion_laboral` `al` on(`al`.`id` = `ai`.`idAsignacionLaboral`)) join `trabajador` `t` on(`t`.`id` = `al`.`idTrabajador`)) join `turno` `tu` on(`tu`.`id` = `al`.`idTurno`)) join `division` `d` on(`d`.`id` = `al`.`idDivision`)) left join `asistencia` `a` on(`a`.`idAsistencia_inasistencia` = `ai`.`id`)) left join `inasistencia` `i` on(`i`.`idAsistencia_inasistencia` = `ai`.`id`)) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
