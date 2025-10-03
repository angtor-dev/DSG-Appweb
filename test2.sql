BEGIN
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
END;