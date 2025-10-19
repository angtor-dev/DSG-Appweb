-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-09-2025 a las 01:00:04
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
CREATE DATABASE IF NOT EXISTS `dsg_db_testmerge` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dsg_db_testmerge`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_gestionar_asignacion_laboral` (IN `p_idTrabajador` INT, IN `p_idDepartamento` INT, IN `p_idTurno` INT, IN `p_idCargo` INT, IN `p_fechaAsignacion` DATE)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_asistencia` (IN `p_id_asistencia_inasistencia` INT, IN `p_fecha` DATE, IN `p_id_trabajador` INT, IN `p_tipo_registro` ENUM('Asistencia','Inasistencia'), IN `p_hora_entrada` TIME, IN `p_hora_salida` TIME, IN `p_tipo_inasistencia` ENUM('Injustificado','Vacaciones','Medico','Emergencia','Judicial','Enfermedad','Muerte De Un Familiar','Otro'), IN `p_descripcion` TEXT)   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_registrar_asistencia_semanal` (IN `p_fecha` DATE, IN `p_cedula` VARCHAR(10) CHARSET utf8mb4, IN `p_codigo_asistencia_inasistencia` VARCHAR(50), IN `p_turno` VARCHAR(50), IN `p_tipo_inasistencia` INT, IN `p_descripcion` TEXT, IN `p_laborable` TINYINT)   BEGIN
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
        
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "paso de la cedula";
        
        
        
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
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajuste`
--

CREATE TABLE `ajuste` (
  `id` int(11) NOT NULL,
  `idInventario` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fechaIncidente` datetime NOT NULL,
  `fechaCreacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `ajuste`
--
DELIMITER $$
CREATE TRIGGER `trg_actualizar_cantidad_articulo` AFTER INSERT ON `ajuste` FOR EACH ROW BEGIN
    UPDATE articulo
    SET cantidad = cantidad + NEW.cantidad
    WHERE id = NEW.idInventario;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`id`, `nombre`) VALUES
(1, 'Hilandera'),
(2, 'Aula H1'),
(3, 'Giraluna'),
(4, 'Planta baja'),
(9, 'G3'),
(14, 'Edificio Rio de las 7 Estrellas'),
(15, 'Aula R1'),
(16, 'Plaza de las banderas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo`
--

CREATE TABLE `articulo` (
  `id` int(11) NOT NULL,
  `idCategoria` int(11) NOT NULL,
  `idMedida` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` decimal(11,2) NOT NULL DEFAULT 0.00,
  `esConsumible` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articulo`
--

INSERT INTO `articulo` (`id`, `idCategoria`, `idMedida`, `nombre`, `descripcion`, `cantidad`, `esConsumible`) VALUES
(1, 1, 1, 'Bolígrafo Azul', 'Bolígrafo de tinta azul, punta fina', 150.00, 1),
(2, 2, 3, 'Jabón Líquido', 'Jabón desinfectante para manos, 1 litro', 21.50, 1),
(3, 3, 1, 'Martillo de Uña', 'Martillo de carpintero con mango de goma', 9.00, 0),
(4, 4, 1, 'Guantes de Seguridad', 'Guantes de protección de nitrilo, talla M', 49.00, 1),
(5, 5, 1, 'Silla Ergonómica', 'Silla de oficina ajustable, color negro', 5.00, 0),
(6, 6, 1, 'Cable HDMI', 'Cable de video HDMI de 2 metros', 30.00, 0),
(7, 7, 1, 'Resma de Papel', 'Papel bond blanco tamaño carta, 500 hojas', 40.00, 1),
(8, 8, 1, 'Clavos surtidos', 'Caja de clavos de diferentes tamaños', 3.75, 1),
(9, 9, 1, 'Bombillo LED', 'Bombillo de bajo consumo, luz blanca', 20.00, 1),
(10, 10, 1, 'Cinta de Teflón', 'Cinta selladora para tuberías', 9.00, 1),
(11, 11, 1, 'Chaleco Reflectivo', 'Chaleco de alta visibilidad, talla L', 15.00, 0),
(12, 12, 1, 'Tijeras de Podar', 'Tijeras de jardín para ramas pequeñas', 7.00, 0),
(13, 13, 1, 'Alcohol Antiséptico', 'Alcohol isopropílico al 70%, 500ml', 30.00, 1),
(14, 14, 1, 'Balón de Baloncesto', 'Balón reglamentario de baloncesto', 3.00, 0),
(15, 15, 1, 'Vasos de Precipitado', 'Juego de vasos de vidrio para laboratorio', 5.00, 0),
(16, 16, 1, 'Juego de Cubiertos', 'Set de cubiertos de acero inoxidable', 10.00, 0),
(17, 17, 3, 'Aceite de Motor', 'Aceite lubricante para motores, 1 litro', 18.00, 1),
(18, 18, 1, 'Hilo de Coser', 'Carrete de hilo de poliéster, color blanco', 25.00, 1),
(19, 19, 1, 'Cuaderno Anillado', 'Cuaderno de tapa dura, 100 hojas', 50.00, 1),
(20, 20, 1, 'Set de Pinceles', 'Juego de pinceles para pintura acrílica', 8.00, 0),
(21, 21, 1, 'Auriculares', 'Auriculares con micrófono, conexión USB', 15.00, 0),
(22, 22, 1, 'Baraja de Cartas', 'Mazo de cartas de póker estándar', 20.00, 1),
(23, 23, 1, 'Lentes de Seguridad', 'Gafas protectoras transparentes', 25.00, 0),
(24, 24, 1, 'Trípode para Cámara', 'Trípode ajustable para cámaras DSLR', 4.00, 0),
(25, 25, 1, 'Limpiador de Metales', 'Producto para limpiar y abrillantar joyas', 10.00, 1),
(26, 26, 1, 'Alimento para Perros', 'Saco de comida seca para perros, 15 kg', 5.00, 1),
(27, 27, 1, 'Saco de Dormir', 'Saco de dormir para camping, temperatura confort 0°C', 2.00, 0),
(28, 28, 1, 'Papel de Regalo', 'Rollo de papel de regalo con diseño festivo', 30.00, 1),
(29, 29, 1, 'Arena para Gatos', 'Saco de arena aglomerante para gatos, 10 kg', 12.00, 1),
(30, 30, 1, 'Etiquetas Adhesivas', 'Rollo de etiquetas autoadhesivas', 50.00, 1);

--
-- Disparadores `articulo`
--
DELIMITER $$
CREATE TRIGGER `trg_after_articulo_update_inventario` AFTER UPDATE ON `articulo` FOR EACH ROW BEGIN
    -- Solo se ejecuta si la cantidad realmente cambió
    IF OLD.cantidad <> NEW.cantidad THEN
        INSERT INTO movimiento (idArticulo, cantidad, antes, despues, fecha)
        VALUES (
            NEW.id,                         -- El ID del artículo modificado
            (NEW.cantidad - OLD.cantidad),  -- La diferencia entre la nueva y la antigua cantidad
            OLD.cantidad,                   -- La cantidad antes de la modificación
            NEW.cantidad,                   -- La cantidad después de la modificación
            NOW()                           -- La fecha y hora actual
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_laboral`
--

CREATE TABLE `asignacion_laboral` (
  `id` int(11) NOT NULL,
  `idTrabajador` int(11) NOT NULL,
  `idDivision` int(11) NOT NULL,
  `idTurno` int(11) NOT NULL,
  `idCargo` int(11) NOT NULL,
  `fechaAsignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaFin` datetime DEFAULT NULL,
  `esActual` tinyint(1) GENERATED ALWAYS AS (if(`fechaFin` is null,1,NULL)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignacion_laboral`
--

INSERT INTO `asignacion_laboral` (`id`, `idTrabajador`, `idDivision`, `idTurno`, `idCargo`, `fechaAsignacion`, `fechaFin`) VALUES
(1, 1, 5, 5, 1, '2024-05-07 00:00:00', '2025-07-03 11:27:33'),
(2, 3, 4, 5, 19, '2024-05-07 00:00:00', NULL),
(3, 6, 3, 4, 12, '2025-12-31 00:00:00', NULL),
(4, 18, 2, 3, 1, '2024-05-07 00:00:00', NULL),
(5, 19, 1, 3, 22, '2024-05-07 00:00:00', NULL),
(6, 20, 1, 2, 28, '2024-05-07 00:00:00', NULL),
(7, 21, 3, 1, 13, '2024-05-07 00:00:00', NULL),
(8, 22, 5, 3, 29, '2024-05-07 00:00:00', NULL),
(9, 23, 1, 1, 30, '2024-05-07 00:00:00', NULL),
(10, 24, 5, 2, 30, '2024-05-07 00:00:00', NULL),
(11, 25, 2, 5, 5, '2024-05-07 00:00:00', NULL),
(12, 26, 4, 1, 12, '2024-05-07 00:00:00', NULL),
(13, 27, 2, 3, 16, '2024-05-07 00:00:00', NULL),
(14, 28, 1, 3, 17, '2024-05-07 00:00:00', NULL),
(15, 29, 1, 5, 25, '2024-05-07 00:00:00', NULL),
(16, 30, 1, 5, 2, '2024-05-07 00:00:00', NULL),
(17, 31, 4, 4, 31, '2024-05-07 00:00:00', NULL),
(18, 32, 4, 3, 16, '2024-05-07 00:00:00', NULL),
(19, 33, 3, 1, 31, '2024-05-07 00:00:00', NULL),
(20, 34, 1, 5, 2, '2024-05-07 00:00:00', NULL),
(21, 35, 2, 3, 19, '2024-05-07 00:00:00', NULL),
(22, 36, 4, 5, 8, '2024-05-07 00:00:00', NULL),
(23, 37, 5, 5, 17, '2024-05-07 00:00:00', NULL),
(24, 38, 3, 2, 8, '2024-05-07 00:00:00', NULL),
(25, 39, 4, 4, 23, '2024-05-07 00:00:00', NULL),
(26, 40, 5, 1, 27, '2024-05-07 00:00:00', NULL),
(27, 41, 5, 2, 16, '2024-05-07 00:00:00', NULL),
(28, 42, 2, 2, 16, '2024-05-07 00:00:00', NULL),
(29, 43, 1, 1, 4, '2024-05-07 00:00:00', NULL),
(30, 44, 1, 4, 2, '2024-05-07 00:00:00', NULL),
(31, 45, 5, 1, 14, '2024-05-07 00:00:00', NULL),
(32, 46, 4, 3, 10, '2024-05-07 00:00:00', NULL),
(33, 47, 5, 2, 13, '2024-05-07 00:00:00', NULL),
(34, 48, 1, 4, 17, '2024-05-07 00:00:00', NULL),
(35, 49, 5, 5, 14, '2024-05-07 00:00:00', NULL),
(36, 50, 4, 4, 19, '2024-05-07 00:00:00', NULL),
(37, 51, 3, 2, 2, '2024-05-07 00:00:00', NULL),
(38, 52, 1, 1, 26, '2024-05-07 00:00:00', NULL),
(39, 53, 5, 4, 2, '2024-05-07 00:00:00', NULL),
(40, 54, 4, 2, 17, '2024-05-07 00:00:00', NULL),
(41, 55, 1, 3, 12, '2024-05-07 00:00:00', NULL),
(42, 56, 2, 4, 21, '2024-05-07 00:00:00', NULL),
(43, 57, 4, 3, 12, '2024-05-07 00:00:00', NULL),
(44, 58, 1, 1, 16, '2024-05-07 00:00:00', NULL),
(45, 59, 3, 2, 5, '2024-05-07 00:00:00', NULL),
(46, 60, 3, 4, 11, '2024-05-07 00:00:00', NULL),
(47, 61, 5, 2, 26, '2024-05-07 00:00:00', NULL),
(48, 62, 3, 3, 19, '2024-05-07 00:00:00', NULL),
(49, 63, 1, 2, 4, '2024-05-07 00:00:00', NULL),
(50, 64, 2, 2, 3, '2024-05-07 00:00:00', NULL),
(51, 65, 3, 3, 12, '2024-05-07 00:00:00', NULL),
(52, 66, 1, 5, 20, '2024-05-07 00:00:00', NULL),
(53, 67, 1, 3, 31, '2024-05-07 00:00:00', NULL),
(54, 68, 5, 4, 15, '2024-05-07 00:00:00', NULL),
(55, 69, 4, 5, 4, '2024-05-07 00:00:00', NULL),
(56, 70, 5, 4, 1, '2024-05-07 00:00:00', NULL),
(57, 71, 4, 5, 11, '2024-05-07 00:00:00', NULL),
(58, 72, 1, 4, 25, '2024-05-07 00:00:00', NULL),
(59, 73, 5, 1, 9, '2024-05-07 00:00:00', NULL),
(60, 74, 1, 1, 12, '2024-05-07 00:00:00', NULL),
(61, 75, 5, 1, 6, '2024-05-07 00:00:00', NULL),
(62, 76, 1, 2, 22, '2024-05-07 00:00:00', NULL),
(63, 77, 5, 2, 10, '2024-05-07 00:00:00', NULL),
(64, 78, 1, 5, 11, '2024-05-07 00:00:00', NULL),
(65, 79, 3, 1, 9, '2024-05-07 00:00:00', NULL),
(66, 80, 1, 4, 15, '2024-05-07 00:00:00', NULL),
(67, 81, 1, 3, 15, '2024-05-07 00:00:00', NULL),
(68, 82, 5, 1, 30, '2024-05-07 00:00:00', NULL),
(69, 83, 2, 2, 3, '2024-05-07 00:00:00', NULL),
(70, 84, 1, 5, 4, '2024-05-07 00:00:00', NULL),
(71, 85, 2, 3, 27, '2024-05-07 00:00:00', NULL),
(72, 86, 3, 5, 8, '2024-05-07 00:00:00', NULL),
(73, 87, 2, 5, 5, '2024-05-07 00:00:00', NULL),
(74, 88, 2, 5, 18, '2024-05-07 00:00:00', NULL),
(75, 89, 1, 4, 12, '2024-05-07 00:00:00', NULL),
(76, 90, 3, 2, 27, '2024-05-07 00:00:00', NULL),
(77, 91, 4, 5, 2, '2024-05-07 00:00:00', NULL),
(78, 92, 3, 4, 10, '2024-05-07 00:00:00', NULL),
(79, 93, 4, 5, 10, '2024-05-07 00:00:00', NULL),
(80, 94, 3, 4, 14, '2024-05-07 00:00:00', NULL),
(81, 95, 3, 1, 15, '2024-05-07 00:00:00', NULL),
(82, 96, 2, 5, 3, '2024-05-07 00:00:00', NULL),
(83, 97, 1, 1, 12, '2024-05-07 00:00:00', NULL),
(84, 98, 5, 5, 20, '2024-05-07 00:00:00', NULL),
(85, 99, 3, 1, 11, '2024-05-07 00:00:00', NULL),
(86, 100, 4, 5, 11, '2024-05-07 00:00:00', NULL),
(87, 101, 4, 1, 6, '2024-05-07 00:00:00', NULL),
(88, 102, 3, 5, 20, '2024-05-07 00:00:00', NULL),
(89, 103, 3, 1, 19, '2024-05-07 00:00:00', NULL),
(90, 104, 5, 4, 11, '2024-05-07 00:00:00', NULL),
(91, 105, 2, 4, 11, '2024-05-07 00:00:00', NULL),
(92, 106, 4, 1, 23, '2024-05-07 00:00:00', NULL),
(93, 107, 2, 5, 18, '2024-05-07 00:00:00', NULL),
(94, 108, 4, 2, 19, '2024-05-07 00:00:00', NULL),
(95, 109, 3, 4, 25, '2024-05-07 00:00:00', NULL),
(96, 110, 2, 1, 14, '2024-05-07 00:00:00', NULL),
(97, 111, 3, 3, 3, '2024-05-07 00:00:00', NULL),
(98, 112, 3, 4, 20, '2024-05-07 00:00:00', NULL),
(99, 113, 2, 1, 26, '2024-05-07 00:00:00', NULL),
(100, 114, 2, 2, 17, '2024-05-07 00:00:00', NULL),
(101, 115, 1, 5, 12, '2024-05-07 00:00:00', NULL),
(102, 116, 4, 3, 11, '2024-05-07 00:00:00', NULL),
(103, 117, 3, 3, 26, '2024-05-07 00:00:00', NULL),
(104, 118, 4, 3, 1, '2024-05-07 00:00:00', NULL),
(105, 119, 5, 2, 23, '2024-05-07 00:00:00', NULL),
(106, 120, 1, 5, 11, '2024-05-07 00:00:00', NULL),
(107, 121, 1, 2, 27, '2024-05-07 00:00:00', NULL),
(108, 122, 1, 3, 10, '1993-12-02 00:00:00', NULL),
(109, 136, 1, 4, 1, '2025-12-31 00:00:00', NULL),
(110, 137, 1, 4, 22, '2025-12-31 00:00:00', NULL),
(137, 171, 1, 1, 1, '2025-06-10 00:00:00', '2025-06-10 17:17:27'),
(139, 171, 1, 1, 1, '2025-06-10 17:39:32', '2025-06-10 18:59:12'),
(140, 171, 2, 1, 1, '2025-06-10 18:59:12', NULL),
(144, 176, 1, 5, 14, '2025-06-10 00:00:00', '2025-06-10 19:29:37'),
(146, 178, 1, 1, 1, '2025-12-31 00:00:00', NULL),
(147, 180, 1, 2, 16, '2025-06-05 00:00:00', '2025-07-02 00:47:31'),
(148, 180, 1, 4, 16, '2025-06-30 22:29:26', '2025-07-02 00:47:31'),
(149, 180, 1, 1, 2, '2025-07-02 00:36:56', '2025-07-02 00:47:31'),
(150, 1, 5, 5, 24, '2025-07-03 11:27:33', '2025-07-03 11:27:51'),
(151, 1, 5, 5, 1, '2025-07-03 11:27:51', NULL),
(152, 181, 2, 1, 6, '2025-09-19 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `idAsistencia_inasistencia` int(11) NOT NULL,
  `horaEntrada` time NOT NULL,
  `horaSalida` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia_inasistencia`
--

CREATE TABLE `asistencia_inasistencia` (
  `id` int(11) NOT NULL,
  `codigoAsistencia` varchar(36) DEFAULT uuid(),
  `idFechaAsistencia` int(11) NOT NULL,
  `idAsignacionLaboral` int(11) NOT NULL,
  `laborable` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia_inasistencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `nivel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`id`, `nombre`, `nivel`) VALUES
(1, 'Director', 1),
(2, 'Aseador', 5),
(3, 'Mensajero interno ', 5),
(4, 'Ayudante de servicios ', 5),
(5, 'Despachador de vehículos ', 5),
(6, 'Ayudante de mantenimiento ', 5),
(7, 'Mensajero externo', 5),
(8, 'Almacenista', 5),
(9, 'Vigilante', 5),
(10, 'Jardinero', 5),
(11, 'Auxiliar de mantenimiento', 5),
(12, 'Chofer', 5),
(13, 'Prensista litógrafo', 5),
(14, 'Litógrafo', 5),
(15, 'Aux. de mtto de equipos de Sonidos, audiovisuales y Electrónicos', 5),
(16, 'Carpintero', 5),
(17, 'Albañil', 5),
(18, 'Plomero', 5),
(19, 'Cocinero', 5),
(20, 'Mecánico en refrigeración', 5),
(21, 'Electricista ', 5),
(22, 'Mecánico ', 5),
(23, 'Herrero-soldador ', 5),
(24, 'Supervisor de servicios', 4),
(25, 'Supervisor de transporte', 4),
(26, 'Supervisor de almacén', 4),
(27, 'Supervisor de vigilancia', 4),
(28, 'Supervisor de prensista', 4),
(29, 'Supervisor de mantenimiento', 4),
(30, 'Supervisor de cocina', 4),
(31, 'Supervisor de electromecánica', 4),
(37, 'Pintor', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(6) NOT NULL DEFAULT '000000'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `color`) VALUES
(1, 'Suministros', 'Artículos de oficina y papelería', 'ff5733'),
(2, 'Limpieza', 'Productos y herramientas de aseo', '33FF57'),
(3, 'Herramientas', 'Utensilios para mantenimiento y reparación', '5733FF'),
(4, 'Seguridad', 'Equipos de protección y prevención', 'FF33A1'),
(5, 'Mobiliario', 'Muebles y accesorios para espacios', '33A1FF'),
(6, 'Electrónica', 'Dispositivos y aparatos electrónicos', 'A1FF33'),
(7, 'Papelería', 'Materiales para escritura y dibujo', 'FF8C33'),
(8, 'Ferretería', 'Artículos de construcción y mejoras', '33FF8C'),
(9, 'Electricidad', 'Componentes eléctricos y cableado', '8C33FF'),
(10, 'Plomería', 'Materiales para sistemas de agua', 'FF33E0'),
(11, 'Vestuario', 'Uniformes y ropa de trabajo', '33E0FF'),
(12, 'Jardinería', 'Implementos para cuidado de áreas verdes', 'E0FF33'),
(13, 'Farmacia', 'Artículos de primeros auxilios y medicinas', 'FF6633'),
(14, 'Deportes', 'Equipamiento para actividades físicas', '33FF66'),
(15, 'Laboratorio', 'Materiales para investigación y pruebas', '6633FF'),
(16, 'Cocina', 'Utensilios y enseres para alimentos', 'FF3399'),
(17, 'Automotriz', 'Repuestos y accesorios para vehículos', '3399FF'),
(18, 'Textiles', 'Telar y materiales de costura', '99FF33'),
(19, 'Libros', 'Material de lectura y consulta', 'FFB833'),
(20, 'Arte', 'Suministros para expresión creativa', '33FFB8'),
(21, 'Música', 'Instrumentos y accesorios musicales', 'B833FF'),
(22, 'Juegos', 'Entretenimiento y pasatiempos', 'FF335E'),
(23, 'Óptica', 'Productos relacionados con la visión', '335EFF'),
(24, 'Fotografía', 'Equipos y accesorios de imagen', '5EFF33'),
(25, 'Joyas', 'Adornos y bisutería', 'FF33B8'),
(26, 'Veterinaria', 'Artículos para el cuidado animal', '33B8FF'),
(27, 'Camping', 'Equipo para actividades al aire libre', 'B8FF33'),
(28, 'Regalos', 'Artículos para obsequios', 'FF338A'),
(29, 'Mascotas', 'Alimentos y accesorios para animales', '338AFF'),
(30, 'Decomiso', 'Objetos de recuperación o reasignación', '8AFF33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `division`
--

CREATE TABLE `division` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `division`
--

INSERT INTO `division` (`id`, `nombre`) VALUES
(1, 'Jardinería y ornato'),
(2, 'Infraestructura'),
(3, 'Herreria'),
(4, 'Plomeria'),
(5, 'Dirección General de Servicios'),
(10, 'Mantenimiento'),
(11, 'Plomeria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrada`
--

CREATE TABLE `entrada` (
  `id` int(11) NOT NULL,
  `fechaEntrada` date NOT NULL,
  `FechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `numeroDocumento` varchar(20) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entradadetalle`
--

CREATE TABLE `entradadetalle` (
  `id` int(11) NOT NULL,
  `idEntrada` int(11) NOT NULL,
  `idArticulo` int(11) NOT NULL,
  `cantidad` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `entradadetalle`
--
DELIMITER $$
CREATE TRIGGER `trg_actualizar_cantidad_articulo_entrada` AFTER INSERT ON `entradadetalle` FOR EACH ROW BEGIN
    UPDATE articulo
    SET cantidad = cantidad + NEW.cantidad
    WHERE id = NEW.idArticulo;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fechaasistencia`
--

CREATE TABLE `fechaasistencia` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cierre` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fechaasistencia`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inasistencia`
--

CREATE TABLE `inasistencia` (
  `idAsistencia_inasistencia` int(11) NOT NULL,
  `tipo` enum('Injustificado','Vacaciones','Medico','Emergencia','Judicial','Enfermedad','Muerte De Un Familiar','Otro') NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inasistencia`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medida`
--

CREATE TABLE `medida` (
  `id` int(11) NOT NULL,
  `unidad` varchar(50) NOT NULL,
  `subUnidad` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medida`
--

INSERT INTO `medida` (`id`, `unidad`, `subUnidad`) VALUES
(1, 'Unidad', 'Und'),
(2, 'Kilogramo', 'Kg'),
(3, 'Litro', 'Lt'),
(4, 'Metros cuadrados', 'Mts2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `idArticulo` int(11) NOT NULL,
  `cantidad` decimal(11,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `antes` int(11) NOT NULL,
  `despues` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimiento`
--

INSERT INTO `movimiento` (`idArticulo`, `cantidad`, `fecha`, `antes`, `despues`) VALUES
(4, -1.00, '2025-07-04 11:18:01', 50, 49),
(8, -5.00, '2025-07-04 11:18:01', 9, 4),
(2, -1.00, '2025-07-04 11:21:06', 26, 25),
(4, -1.00, '2025-07-04 11:23:18', 49, 48),
(3, -1.00, '2025-07-04 11:23:18', 10, 9),
(4, 1.00, '2025-07-04 11:26:29', 48, 49),
(10, -2.00, '2025-07-04 13:16:06', 12, 10),
(10, -1.00, '2025-09-19 14:13:17', 10, 9),
(2, -3.00, '2025-09-19 14:13:17', 25, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recurso`
--

CREATE TABLE `recurso` (
  `id` int(11) NOT NULL,
  `idTarea` int(11) NOT NULL,
  `idArticulo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `devolucion` tinyint(4) DEFAULT NULL,
  `cantidadDevolucion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recurso`
--

INSERT INTO `recurso` (`id`, `idTarea`, `idArticulo`, `cantidad`, `devolucion`, `cantidadDevolucion`) VALUES
(3, 32, 4, 0, 1, 1),
(4, 32, 8, 5, 1, 0),
(5, 33, 2, 1, 0, 0),
(6, 34, 4, 1, 0, 0),
(7, 34, 3, 1, 0, 0),
(8, 35, 10, 2, 0, 0),
(9, 35, 3, 18, 0, 0),
(10, 36, 10, 1, 0, 0),
(11, 36, 2, 3, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subarea`
--

CREATE TABLE `subarea` (
  `idAreaPadre` int(11) NOT NULL,
  `idAreaHijo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subarea`
--

INSERT INTO `subarea` (`idAreaPadre`, `idAreaHijo`) VALUES
(3, 9),
(14, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subdivisiones`
--

CREATE TABLE `subdivisiones` (
  `idPadre` int(11) NOT NULL,
  `idHijo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subdivisiones`
--

INSERT INTO `subdivisiones` (`idPadre`, `idHijo`) VALUES
(2, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `id` int(11) NOT NULL,
  `idArea` int(11) NOT NULL,
  `idDepartamento` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `fechaCreacion` datetime DEFAULT current_timestamp(),
  `fecha_inicio` datetime DEFAULT NULL,
  `estado_tarea` varchar(15) NOT NULL,
  `es_comun` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea`
--

INSERT INTO `tarea` (`id`, `idArea`, `idDepartamento`, `descripcion`, `fechaCreacion`, `fecha_inicio`, `estado_tarea`, `es_comun`) VALUES
(30, 3, 1, 'desmalezado', '2025-07-04 11:05:19', '2025-07-04 00:00:00', 'cancelado', 0),
(32, 2, 3, 'la ventana del salón se callo por oxido', '2025-07-04 11:18:01', '2025-07-04 00:00:00', 'evaluada', 0),
(33, 1, 4, 'arreglar toma de agua', '2025-07-04 11:21:06', '2025-07-02 00:00:00', 'activo', 0),
(34, 9, 2, 'lijar y pintar puerta', '2025-07-04 11:23:18', '2025-07-04 00:00:00', 'vencida', 0),
(35, 3, 3, 'Se repararan sillas en giraluna', '2025-07-04 13:16:06', '2025-07-04 00:00:00', 'activo', 0),
(36, 16, 4, 'plaza de las banderas', '2025-09-19 14:13:17', '2025-09-22 00:00:00', 'activo', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_personal`
--

CREATE TABLE `tarea_personal` (
  `idTarea` int(11) NOT NULL,
  `idAsignacionLaboral` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea_personal`
--

INSERT INTO `tarea_personal` (`idTarea`, `idAsignacionLaboral`) VALUES
(30, 64),
(32, 46),
(33, 2),
(34, 11),
(35, 7),
(36, 40),
(36, 102);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea_validacion`
--

CREATE TABLE `tarea_validacion` (
  `id` int(11) NOT NULL,
  `idTarea` int(11) NOT NULL,
  `idSupervisor` int(11) NOT NULL,
  `fechaAsignado` datetime DEFAULT current_timestamp(),
  `fechaEval` datetime DEFAULT NULL,
  `evalSupervisor` set('buenobueno','buenomedio','buenomalo','mediomedio','mediomalo','malomalo') DEFAULT NULL,
  `evalSuperior` set('buenobueno','buenomedio','buenomalo','mediomedio','mediomalo','malomalo') DEFAULT NULL,
  `observacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarea_validacion`
--

INSERT INTO `tarea_validacion` (`id`, `idTarea`, `idSupervisor`, `fechaAsignado`, `fechaEval`, `evalSupervisor`, `evalSuperior`, `observacion`) VALUES
(21, 30, 29, '2025-07-04 11:05:19', NULL, NULL, NULL, NULL),
(23, 32, 33, '2025-07-04 11:18:01', '2025-07-04 11:26:29', 'buenobueno', NULL, '[Observación supervisor]: terminada'),
(24, 33, 31, '2025-07-04 11:21:06', NULL, NULL, NULL, NULL),
(25, 34, 113, '2025-07-04 11:23:18', NULL, NULL, NULL, NULL),
(26, 35, 33, '2025-07-04 13:16:06', NULL, NULL, NULL, NULL),
(27, 36, 31, '2025-09-19 14:13:17', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajador`
--

CREATE TABLE `trabajador` (
  `id` int(11) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(11) NOT NULL,
  `fechaIngreso` date NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajador`
--

INSERT INTO `trabajador` (`id`, `cedula`, `nombre`, `apellido`, `telefono`, `fechaIngreso`, `estado`) VALUES
(1, '00000001', 'Admin', 'DSG', '04145555555', '2024-05-07', 1),
(3, '34785435', 'Josep', 'Marshal', '04263525659', '2024-05-07', 1),
(6, '00000002', 'xavier', 'sanchez', '04145555555', '2025-12-31', 1),
(18, '00000008', 'Anabel', 'sanchez', '04145555555', '2024-05-07', 1),
(19, '00000009', 'robert', 'camacaro', '04145555555', '2024-05-07', 1),
(20, '00000010', 'valeria', 'camacaro', '04145555555', '2024-05-07', 1),
(21, '49575485', 'Garrek', 'Mutlow', '04145555555', '2024-05-07', 1),
(22, '03891538', 'Roger', 'Divina', '04145555555', '2024-05-07', 1),
(23, '41617507', 'Alisa', 'Gleaves', '04145555555', '2024-05-07', 1),
(24, '64354956', 'Keven', 'Adhams', '04145555555', '2024-05-07', 1),
(25, '34054048', 'Rikki', 'Sprason', '04145555555', '2024-05-07', 1),
(26, '43398951', 'Gonzales', 'Rzehorz', '04145555555', '2024-05-07', 1),
(27, '52169881', 'Tiphanie', 'Weatherley', '04145555555', '2024-05-07', 1),
(28, '34013415', 'Sollie', 'Haire', '04145555555', '2024-05-07', 1),
(29, '81459845', 'Shelba', 'Margrett', '04145555555', '2024-05-07', 1),
(30, '48696551', 'Curry', 'Mattke', '04145555555', '2024-05-07', 1),
(31, '54883223', 'Elvyn', 'Gothup', '04145555555', '2024-05-07', 1),
(32, '31019514', 'Inna', 'Huffey', '04145555555', '2024-05-07', 1),
(33, '42774093', 'Jessamine', 'Brennen', '04145555555', '2024-05-07', 1),
(34, '86134297', 'Elia', 'Josilowski', '04145555555', '2024-05-07', 1),
(35, '41663200', 'Flinn', 'Grellier', '04145555555', '2024-05-07', 1),
(36, '67900531', 'Loren', 'Souter', '04145555555', '2024-05-07', 1),
(37, '47180866', 'Reinwald', 'Sawnwy', '04145555555', '2024-05-07', 1),
(38, '77555389', 'Tonia', 'Brimley', '04145555555', '2024-05-07', 1),
(39, '16433005', 'Charmaine', 'Calcutt', '04145555555', '2024-05-07', 1),
(40, '14084206', 'Nari', 'Seton', '04145555555', '2024-05-07', 1),
(41, '36379648', 'Nancee', 'Apark', '04145555555', '2024-05-07', 1),
(42, '11594520', 'Constantino', 'Ingolotti', '04145555555', '2024-05-07', 1),
(43, '71873411', 'Robinson', 'Hendriksen', '04145555555', '2024-05-07', 1),
(44, '19604089', 'Cherilyn', 'Pettie', '04145555555', '2024-05-07', 1),
(45, '46621075', 'Freida', 'De Brett', '04145555555', '2024-05-07', 1),
(46, '98533622', 'Ann-marie', 'Alesin', '04145555555', '2024-05-07', 1),
(47, '48077223', 'Baxter', 'Golly', '04145555555', '2024-05-07', 1),
(48, '77518450', 'Johnath', 'Bignell', '04145555555', '2024-05-07', 1),
(49, '79258263', 'Erhard', 'Stennett', '04145555555', '2024-05-07', 1),
(50, '37923019', 'Maximilien', 'Bedminster', '04145555555', '2024-05-07', 1),
(51, '22912556', 'Piggy', 'Piatto', '04145555555', '2024-05-07', 1),
(52, '75219093', 'Megan', 'Draper', '04145555555', '2024-05-07', 1),
(53, '75369516', 'Alberta', 'Delany', '04145555555', '2024-05-07', 1),
(54, '36678526', 'Rubie', 'McKendry', '04145555555', '2024-05-07', 1),
(55, '23132369', 'Pauline', 'Bircher', '04145555555', '2024-05-07', 1),
(56, '22813053', 'Jody', 'Bettley', '04145555555', '2024-05-07', 1),
(57, '66250505', 'Kipp', 'Deaton', '04145555555', '2024-05-07', 1),
(58, '89813166', 'Sherline', 'Broxton', '04145555555', '2024-05-07', 1),
(59, '46619780', 'Isaac', 'Skylett', '04145555555', '2024-05-07', 1),
(60, '15892074', 'Edyth', 'Branscombe', '04145555555', '2024-05-07', 1),
(61, '56106268', 'Carmella', 'Worral', '04145555555', '2024-05-07', 1),
(62, '24194059', 'Shay', 'Kittle', '04145555555', '2024-05-07', 1),
(63, '73121570', 'Valencia', 'McGlade', '04145555555', '2024-05-07', 1),
(64, '13098114', 'Shae', 'Ganley', '04145555555', '2024-05-07', 1),
(65, '00553829', 'Hillard', 'Tomczykiewicz', '04145555555', '2024-05-07', 1),
(66, '12272250', 'Annabel', 'Swanne', '04145555555', '2024-05-07', 1),
(67, '33734939', 'Lion', 'Nettleship', '04145555555', '2024-05-07', 1),
(68, '61753100', 'Augusto', 'Beaty', '04145555555', '2024-05-07', 1),
(69, '20999008', 'Abdel', 'Troker', '04145555555', '2024-05-07', 1),
(70, '85159679', 'Stevie', 'Setch', '04145555555', '2024-05-07', 1),
(71, '93083049', 'Katheryn', 'Klimuk', '04145555555', '2024-05-07', 1),
(72, '31216465', 'Carney', 'Aldhouse', '04145555555', '2024-05-07', 1),
(73, '96427642', 'Hendrika', 'Decent', '04145555555', '2024-05-07', 1),
(74, '03767510', 'Huey', 'Lune', '04145555555', '2024-05-07', 1),
(75, '47315271', 'Dyna', 'Lynam', '04145555555', '2024-05-07', 1),
(76, '14448463', 'Bettina', 'Holt', '04145555555', '2024-05-07', 1),
(77, '42808619', 'Rebeca', 'Geri', '04145555555', '2024-05-07', 1),
(78, '50131356', 'Rhianna', 'Maryet', '04145555555', '2024-05-07', 1),
(79, '86754669', 'Amandie', 'Lilion', '04145555555', '2024-05-07', 1),
(80, '67270756', 'Wylie', 'Smillie', '04145555555', '2024-05-07', 1),
(81, '67073050', 'Tamma', 'Armsden', '04145555555', '2024-05-07', 1),
(82, '44690436', 'Garald', 'Jacques', '04145555555', '2024-05-07', 1),
(83, '89604550', 'Brynne', 'Gullan', '04145555555', '2024-05-07', 1),
(84, '18495618', 'Ranice', 'Cescoti', '04145555555', '2024-05-07', 1),
(85, '52545285', 'Sabine', 'Scarff', '04145555555', '2024-05-07', 1),
(86, '65260208', 'Gene', 'Boldra', '04145555555', '2024-05-07', 1),
(87, '54692589', 'Georas', 'Bernli', '04145555555', '2024-05-07', 1),
(88, '07780242', 'Tyrus', 'Stenhouse', '04145555555', '2024-05-07', 1),
(89, '54306791', 'Nani', 'Youens', '04145555555', '2024-05-07', 1),
(90, '04574043', 'Chen', 'Maryon', '04145555555', '2024-05-07', 1),
(91, '97275863', 'Joshua', 'Galvin', '04145555555', '2024-05-07', 1),
(92, '75150644', 'Lucho', 'Orteaux', '04145555555', '2024-05-07', 1),
(93, '73407243', 'Ban', 'Tunder', '04145555555', '2024-05-07', 1),
(94, '28573559', 'Griswold', 'Brixey', '04145555555', '2024-05-07', 1),
(95, '14407144', 'Reinald', 'Wank', '04145555555', '2024-05-07', 1),
(96, '10281391', 'Neely', 'McConway', '04145555555', '2024-05-07', 1),
(97, '41386196', 'Eydie', 'Raveau', '04145555555', '2024-05-07', 1),
(98, '38159614', 'Denise', 'Dener', '04145555555', '2024-05-07', 1),
(99, '19846842', 'Willi', 'Lipp', '04145555555', '2024-05-07', 1),
(100, '75649190', 'Kerwinn', 'Trembey', '04145555555', '2024-05-07', 1),
(101, '25855381', 'Viole', 'Wooles', '04145555555', '2024-05-07', 1),
(102, '44808792', 'Fitz', 'Campaigne', '04145555555', '2024-05-07', 1),
(103, '64642990', 'Obediah', 'Essame', '04145555555', '2024-05-07', 1),
(104, '68462818', 'Alexandro', 'Buckby', '04145555555', '2024-05-07', 1),
(105, '89796052', 'Desdemona', 'Lemmon', '04145555555', '2024-05-07', 1),
(106, '49035052', 'Nancee', 'Semper', '04145555555', '2024-05-07', 1),
(107, '09326592', 'Eryn', 'MacCarrick', '04145555555', '2024-05-07', 1),
(108, '37051913', 'Margo', 'Shinton', '04145555555', '2024-05-07', 1),
(109, '16294472', 'Brandice', 'Bolver', '04145555555', '2024-05-07', 1),
(110, '33116833', 'Leola', 'Skelbeck', '04145555555', '2024-05-07', 1),
(111, '78796099', 'Susanetta', 'Dennistoun', '04145555555', '2024-05-07', 1),
(112, '00186827', 'Willamina', 'Connochie', '04145555555', '2024-05-07', 1),
(113, '71649128', 'Caddric', 'Fawkes', '04145555555', '2024-05-07', 1),
(114, '07350118', 'Electra', 'Jacobsen', '04145555555', '2024-05-07', 1),
(115, '13106535', 'Jonah', 'Moreno', '04145555555', '2024-05-07', 1),
(116, '85204927', 'Pat', 'Dodds', '04145555555', '2024-05-07', 1),
(117, '43713373', 'Mark', 'Wragge', '04145555555', '2024-05-07', 1),
(118, '06028832', 'Iggy', 'Ashworth', '04145555555', '2024-05-07', 1),
(119, '79734534', 'Clareta', 'Faichney', '04145555555', '2024-05-07', 1),
(120, '45915209', 'Wilbur', 'Tuplin', '04145555555', '2024-05-07', 1),
(121, '00000011', 'probando ', 'cossas', '04145555555', '2024-05-07', 1),
(122, '00000007', 'Pablo ', 'Escobar', '04145555555', '1993-12-02', 1),
(136, '1111111', 'probando nuevo formulario', 'Queso', '04145555555', '2025-12-31', 1),
(137, '11111112', 'probando nuevo formulario', 'Queso', '04145555555', '2025-12-31', 1),
(171, '00000000', 'xavier', 'sanchez', '04145555555', '2025-06-10', 1),
(176, '0000111', 'xavier', 'sanchez', '04145555555', '2025-06-10', 0),
(178, '00000777', 'probando nuevo formulario', 'Escobar', '04145555555', '2025-12-31', 1),
(180, '00000003', 'xavier', 'suarez', '05135135131', '2025-07-02', 0),
(181, '18843756', 'Adsalom', 'Rodriguez', '04120580061', '2025-09-19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `id` int(11) NOT NULL,
  `codigo` varchar(36) DEFAULT uuid(),
  `nombre` varchar(50) NOT NULL,
  `horario_entrada` time NOT NULL,
  `horario_salida` time NOT NULL,
  `lunes` tinyint(1) DEFAULT 0,
  `martes` tinyint(1) DEFAULT 0,
  `miercoles` tinyint(1) DEFAULT 0,
  `jueves` tinyint(1) DEFAULT 0,
  `viernes` tinyint(1) DEFAULT 0,
  `sabado` tinyint(1) DEFAULT 0,
  `domingo` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`id`, `codigo`, `nombre`, `horario_entrada`, `horario_salida`, `lunes`, `martes`, `miercoles`, `jueves`, `viernes`, `sabado`, `domingo`) VALUES
(1, 'e1616fed-8d0a-11f0-91e8-d481d7968c88', 'Mañana', '08:00:00', '12:00:00', 1, 1, 1, 1, 0, 0, 0),
(2, 'e1617129-8d0a-11f0-91e8-d481d7968c88', 'Tarde', '13:00:00', '17:00:00', 1, 1, 1, 1, 0, 0, 0),
(3, 'e16171b0-8d0a-11f0-91e8-d481d7968c88', 'Noche', '18:00:00', '22:00:00', 1, 1, 1, 1, 0, 0, 0),
(4, 'e161720c-8d0a-11f0-91e8-d481d7968c88', 'Fin de semana', '08:00:00', '17:00:00', 0, 0, 0, 0, 1, 1, 0),
(5, 'e1617263-8d0a-11f0-91e8-d481d7968c88', 'Especial', '08:00:00', '17:00:00', 1, 1, 1, 1, 1, 1, 0),
(14, 'ec233a83-8dea-11f0-91e8-d481d7968c88', 'Para Pruebas hhhhh', '22:08:00', '22:09:00', 1, 0, 0, 0, 0, 0, 0);

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

CREATE ALGORITHM=TEMPTABLE DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_asistencias`  AS SELECT `t`.`cedula` AS `cedula`, `t`.`nombre` AS `nombre`, `t`.`apellido` AS `apellido`, `fa`.`fecha` AS `fecha`, `a`.`horaEntrada` AS `horaEntrada`, `a`.`horaSalida` AS `horaSalida`, `i`.`tipo` AS `tipo`, `i`.`descripcion` AS `descripcion`, `d`.`id` AS `idDivision`, `d`.`nombre` AS `division`, `tu`.`id` AS `idTurno`, `tu`.`nombre` AS `turno`, if(`a`.`idAsistencia_inasistencia` is not null,1,0) AS `esAsistencia` FROM (((((((`asistencia_inasistencia` `ai` join `fechaasistencia` `fa` on(`fa`.`id` = `ai`.`idFechaAsistencia`)) join `asignacion_laboral` `al` on(`al`.`id` = `ai`.`idAsignacionLaboral`)) join `trabajador` `t` on(`t`.`id` = `al`.`idTrabajador`)) join `turno` `tu` on(`tu`.`id` = `al`.`idTurno`)) join `division` `d` on(`d`.`id` = `al`.`idDivision`)) left join `asistencia` `a` on(`a`.`idAsistencia_inasistencia` = `ai`.`id`)) left join `inasistencia` `i` on(`i`.`idAsistencia_inasistencia` = `ai`.`id`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ajuste`
--
ALTER TABLE `ajuste`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idInventario` (`idInventario`);

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCategoria` (`idCategoria`),
  ADD KEY `idMedida` (`idMedida`);

--
-- Indices de la tabla `asignacion_laboral`
--
ALTER TABLE `asignacion_laboral`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idTrabajador_2` (`idTrabajador`,`esActual`),
  ADD KEY `idCargo` (`idCargo`),
  ADD KEY `idTurno` (`idTurno`),
  ADD KEY `idDepartamento` (`idDivision`),
  ADD KEY `idTrabajador` (`idTrabajador`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`idAsistencia_inasistencia`);

--
-- Indices de la tabla `asistencia_inasistencia`
--
ALTER TABLE `asistencia_inasistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idFechaAsistencia_2` (`idFechaAsistencia`,`idAsignacionLaboral`),
  ADD UNIQUE KEY `codigoAsistencia` (`codigoAsistencia`),
  ADD KEY `idFechaAsistencia` (`idFechaAsistencia`),
  ADD KEY `idAsignacionLaboral` (`idAsignacionLaboral`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `division`
--
ALTER TABLE `division`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `entrada`
--
ALTER TABLE `entrada`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `entradadetalle`
--
ALTER TABLE `entradadetalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idEntrada` (`idEntrada`),
  ADD KEY `idArticulo` (`idArticulo`);

--
-- Indices de la tabla `fechaasistencia`
--
ALTER TABLE `fechaasistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fecha` (`fecha`);

--
-- Indices de la tabla `inasistencia`
--
ALTER TABLE `inasistencia`
  ADD PRIMARY KEY (`idAsistencia_inasistencia`);

--
-- Indices de la tabla `medida`
--
ALTER TABLE `medida`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD KEY `idInventario` (`idArticulo`);

--
-- Indices de la tabla `recurso`
--
ALTER TABLE `recurso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTarea` (`idTarea`),
  ADD KEY `idInventario` (`idArticulo`);

--
-- Indices de la tabla `subarea`
--
ALTER TABLE `subarea`
  ADD KEY `idAreaPadre` (`idAreaPadre`),
  ADD KEY `idAreaHijo` (`idAreaHijo`);

--
-- Indices de la tabla `subdivisiones`
--
ALTER TABLE `subdivisiones`
  ADD KEY `idPadre` (`idPadre`),
  ADD KEY `idHijo` (`idHijo`);

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idArea` (`idArea`),
  ADD KEY `idDepartamento` (`idDepartamento`);

--
-- Indices de la tabla `tarea_personal`
--
ALTER TABLE `tarea_personal`
  ADD PRIMARY KEY (`idTarea`,`idAsignacionLaboral`),
  ADD KEY `idTrabajador` (`idAsignacionLaboral`);

--
-- Indices de la tabla `tarea_validacion`
--
ALTER TABLE `tarea_validacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTarea` (`idTarea`),
  ADD KEY `idTrabajador` (`idSupervisor`);

--
-- Indices de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ajuste`
--
ALTER TABLE `ajuste`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `articulo`
--
ALTER TABLE `articulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `asignacion_laboral`
--
ALTER TABLE `asignacion_laboral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT de la tabla `asistencia_inasistencia`
--
ALTER TABLE `asistencia_inasistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26465;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `division`
--
ALTER TABLE `division`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `entrada`
--
ALTER TABLE `entrada`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entradadetalle`
--
ALTER TABLE `entradadetalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fechaasistencia`
--
ALTER TABLE `fechaasistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=364;

--
-- AUTO_INCREMENT de la tabla `medida`
--
ALTER TABLE `medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `recurso`
--
ALTER TABLE `recurso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `tarea`
--
ALTER TABLE `tarea`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `tarea_validacion`
--
ALTER TABLE `tarea_validacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `trabajador`
--
ALTER TABLE `trabajador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ajuste`
--
ALTER TABLE `ajuste`
  ADD CONSTRAINT `ajuste_ibfk_1` FOREIGN KEY (`idInventario`) REFERENCES `articulo` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD CONSTRAINT `articulo_ibfk_1` FOREIGN KEY (`idMedida`) REFERENCES `medida` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `articulo_ibfk_2` FOREIGN KEY (`idCategoria`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asignacion_laboral`
--
ALTER TABLE `asignacion_laboral`
  ADD CONSTRAINT `asignacion_laboral_ibfk_1` FOREIGN KEY (`idTrabajador`) REFERENCES `trabajador` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_laboral_ibfk_2` FOREIGN KEY (`idDivision`) REFERENCES `division` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_laboral_ibfk_3` FOREIGN KEY (`idCargo`) REFERENCES `cargo` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignacion_laboral_ibfk_4` FOREIGN KEY (`idTurno`) REFERENCES `turno` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`idAsistencia_inasistencia`) REFERENCES `asistencia_inasistencia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `asistencia_inasistencia`
--
ALTER TABLE `asistencia_inasistencia`
  ADD CONSTRAINT `asistencia_inasistencia_ibfk_2` FOREIGN KEY (`idFechaAsistencia`) REFERENCES `fechaasistencia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asistencia_inasistencia_ibfk_3` FOREIGN KEY (`idAsignacionLaboral`) REFERENCES `asignacion_laboral` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `entradadetalle`
--
ALTER TABLE `entradadetalle`
  ADD CONSTRAINT `entradadetalle_ibfk_1` FOREIGN KEY (`idEntrada`) REFERENCES `entrada` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `entradadetalle_ibfk_2` FOREIGN KEY (`idArticulo`) REFERENCES `articulo` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `inasistencia`
--
ALTER TABLE `inasistencia`
  ADD CONSTRAINT `inasistencia_ibfk_1` FOREIGN KEY (`idAsistencia_inasistencia`) REFERENCES `asistencia_inasistencia` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `movimiento_ibfk_1` FOREIGN KEY (`idArticulo`) REFERENCES `articulo` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `recurso`
--
ALTER TABLE `recurso`
  ADD CONSTRAINT `recurso_ibfk_1` FOREIGN KEY (`idTarea`) REFERENCES `tarea_validacion` (`idTarea`) ON UPDATE CASCADE,
  ADD CONSTRAINT `recurso_ibfk_2` FOREIGN KEY (`idArticulo`) REFERENCES `articulo` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `subarea`
--
ALTER TABLE `subarea`
  ADD CONSTRAINT `subarea_ibfk_1` FOREIGN KEY (`idAreaPadre`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subarea_ibfk_2` FOREIGN KEY (`idAreaHijo`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `subdivisiones`
--
ALTER TABLE `subdivisiones`
  ADD CONSTRAINT `subdivisiones_ibfk_1` FOREIGN KEY (`idPadre`) REFERENCES `division` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subdivisiones_ibfk_2` FOREIGN KEY (`idHijo`) REFERENCES `division` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `tarea_ibfk_1` FOREIGN KEY (`idArea`) REFERENCES `area` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tarea_ibfk_2` FOREIGN KEY (`idDepartamento`) REFERENCES `division` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarea_personal`
--
ALTER TABLE `tarea_personal`
  ADD CONSTRAINT `tarea_personal_ibfk_1` FOREIGN KEY (`idTarea`) REFERENCES `tarea` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tarea_personal_ibfk_2` FOREIGN KEY (`idAsignacionLaboral`) REFERENCES `asignacion_laboral` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarea_validacion`
--
ALTER TABLE `tarea_validacion`
  ADD CONSTRAINT `tarea_validacion_ibfk_1` FOREIGN KEY (`idTarea`) REFERENCES `tarea` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tarea_validacion_ibfk_2` FOREIGN KEY (`idSupervisor`) REFERENCES `trabajador` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
